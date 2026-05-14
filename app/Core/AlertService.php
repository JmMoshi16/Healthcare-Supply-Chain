<?php

namespace App\Core;

use App\Models\Batch;
use App\Models\Medicine;
use App\Models\User;
use App\Models\Stock;
use App\Models\Notification;

/**
 * Alert Service - Handles all automated email notifications
 */
class AlertService
{
    private Mailer $mailer;
    private Batch $batchModel;
    private Medicine $medicineModel;
    private User $userModel;
    private Stock $stockModel;

    public function __construct()
    {
        $this->mailer = new Mailer();
        $this->batchModel = new Batch();
        $this->medicineModel = new Medicine();
        $this->userModel = new User();
        $this->stockModel = new Stock();
    }

    /**
     * Send expiry alerts for batches expiring in X days
     */
    public function sendExpiryAlerts(): array
    {
        $expiryDays = explode(',', $_ENV['ALERT_EXPIRY_DAYS'] ?? '30,15,7');
        $results = [];

        foreach ($expiryDays as $days) {
            $days    = (int) trim($days);
            $batches = $this->getExpiringBatches($days);

            if (!empty($batches)) {
                $recipients = $this->getAlertRecipients(['superadmin', 'manager']);
                $subject    = "⚠️ Medicine Expiry Alert - {$days} Days";
                $body       = $this->renderTemplate('expiry_alert', [
                    'batches' => $batches,
                    'days'    => $days,
                ]);

                $sent = false;
                foreach ($recipients as $recipient) {
                    $s = $this->mailer->send($recipient['email'], $subject, $body);
                    $results[] = [
                        'type'      => 'expiry',
                        'days'      => $days,
                        'recipient' => $recipient['email'],
                        'success'   => $s,
                    ];
                    if ($s) $sent = true;
                }

                // Persist notification so it shows in the bell panel
                if ($sent) {
                    $count   = count($batches);
                    $first   = $batches[0];
                    $urgency = $days <= 7 ? 'critical' : ($days <= 15 ? 'high' : 'medium');
                    Notification::broadcast(
                        'email_alert',
                        $urgency,
                        'envelope-exclamation',
                        "Expiry Alert Sent ({$days} days)",
                        "Email sent for {$count} batch" . ($count !== 1 ? 'es' : '') . " expiring within {$days} days",
                        '/batches'
                    );
                }
            }
        }

        return $results;
    }

    /**
     * Send low stock alerts
     */
    public function sendLowStockAlerts(): array
    {
        $threshold = (int) ($_ENV['ALERT_LOW_STOCK_THRESHOLD'] ?? 10);
        $medicines = $this->medicineModel->getLowStock($threshold);
        $results   = [];

        if (!empty($medicines)) {
            $recipients = $this->getAlertRecipients(['superadmin', 'manager']);
            $subject    = "🔴 Low Stock Alert - Action Required";
            $body       = $this->renderTemplate('low_stock_alert', [
                'medicines' => $medicines,
                'threshold' => $threshold,
            ]);

            $sent = false;
            foreach ($recipients as $recipient) {
                $s = $this->mailer->send($recipient['email'], $subject, $body);
                $results[] = [
                    'type'      => 'low_stock',
                    'recipient' => $recipient['email'],
                    'success'   => $s,
                ];
                if ($s) $sent = true;
            }

            // Persist notification so it shows in the bell panel
            if ($sent) {
                $count = count($medicines);
                Notification::broadcast(
                    'email_alert',
                    'high',
                    'envelope-exclamation',
                    'Low Stock Alert Sent',
                    "Email sent for {$count} medicine" . ($count !== 1 ? 's' : '') . " below {$threshold} units",
                    '/medicines'
                );
            }
        }

        return $results;
    }

    /**
     * Send batch recall notification
     */
    public function sendBatchRecallNotification(int $batchId, string $reason, int $initiatedBy): bool
    {
        $batch = $this->batchModel->find($batchId);
        if (!$batch) {
            return false;
        }

        $medicine  = $this->medicineModel->find($batch['medicine_id']);
        $initiator = $this->userModel->find($initiatedBy);

        $batch['medicine_name'] = $medicine['name'] ?? 'Unknown';

        $recipients = $this->getAlertRecipients(['superadmin', 'manager', 'staff']);
        $subject    = "🚨 URGENT: Batch Recall - " . $batch['batch_number'];
        $body       = $this->renderTemplate('batch_recall', [
            'batch'        => $batch,
            'reason'       => $reason,
            'initiated_by' => $initiator['fullname'] ?? 'System',
        ]);

        $results = $this->mailer->sendBulk(
            array_column($recipients, 'email'),
            $subject,
            $body
        );

        $success = count($results['success']) > 0;

        // Persist notification in the bell panel
        if ($success) {
            Notification::broadcast(
                'batch_recall',
                'critical',
                'exclamation-triangle-fill',
                '🚨 Batch Recalled: ' . $batch['batch_number'],
                ($batch['medicine_name'] ?? 'Unknown') . ' recalled — ' . $reason,
                '/batches/' . $batchId
            );
        }

        return $success;
    }

    /**
     * Send welcome email to new user
     */
    public function sendWelcomeEmail(array $user): bool
    {
        $subject = "Welcome to Healthcare Supply Chain!";
        $body    = $this->renderTemplate('welcome', ['user' => $user]);

        $sent = $this->mailer->send($user['email'], $subject, $body);

        // Persist notification for all admins
        if ($sent) {
            Notification::broadcast(
                'welcome',
                'low',
                'person-check-fill',
                'New User Registered',
                ($user['fullname'] ?? 'A new user') . ' joined as ' . ucfirst($user['role'] ?? 'staff'),
                '/users'
            );
        }

        return $sent;
    }

    /**
     * Send daily inventory report
     */
    public function sendDailyReport(): array
    {
        return $this->sendInventoryReport('daily');
    }

    /**
     * Send weekly inventory report
     */
    public function sendWeeklyReport(): array
    {
        return $this->sendInventoryReport('weekly');
    }

    /**
     * Send inventory report (daily/weekly)
     */
    private function sendInventoryReport(string $type): array
    {
        $stats      = $this->getInventoryStats($type);
        $recipients = $this->getAlertRecipients(['superadmin', 'manager']);

        $subject = ucfirst($type) . " Inventory Report - " . date('M d, Y');
        $body    = $this->renderTemplate('inventory_report', array_merge($stats, [
            'reportType'     => $type,
            'startDate'      => $stats['start_date'],
            'endDate'        => $stats['end_date'],
            'nextReportDate' => $type === 'daily'
                ? date('M d, Y', strtotime('+1 day'))
                : date('M d, Y', strtotime('+1 week')),
        ]));

        $results = [];
        $sent    = false;
        foreach ($recipients as $recipient) {
            $s = $this->mailer->send($recipient['email'], $subject, $body);
            $results[] = [
                'type'      => $type . '_report',
                'recipient' => $recipient['email'],
                'success'   => $s,
            ];
            if ($s) $sent = true;
        }

        // Persist notification in bell panel
        if ($sent) {
            $label = $type === 'daily' ? 'Daily' : 'Weekly';
            Notification::broadcast(
                'report',
                'low',
                'file-earmark-bar-graph-fill',
                "{$label} Report Sent",
                "Inventory {$label} report emailed to " . count($recipients) . ' recipient' . (count($recipients) !== 1 ? 's' : ''),
                '/dashboard'
            );
        }

        return $results;
    }

    /**
     * Get batches expiring in X days
     */
    private function getExpiringBatches(int $days): array
    {
        $targetDate = date('Y-m-d', strtotime("+{$days} days"));
        $sql = "
            SELECT b.*, m.name as medicine_name
            FROM batches b
            JOIN medicines m ON m.id = b.medicine_id
            WHERE b.expiry_date <= ?
            AND b.expiry_date >= CURDATE()
            AND b.status = 'active'
            AND b.current_quantity > 0
            ORDER BY b.expiry_date ASC
        ";
        
        return \App\Core\Database::query($sql, [$targetDate])->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get alert recipients by roles
     */
    private function getAlertRecipients(array $roles): array
    {
        $placeholders = implode(',', array_fill(0, count($roles), '?'));
        $sql = "SELECT id, fullname, email, role FROM users WHERE role IN ({$placeholders}) AND is_active = 1";
        
        return \App\Core\Database::query($sql, $roles)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get inventory statistics
     */
    private function getInventoryStats(string $type): array
    {
        $startDate = $type === 'daily' ? date('Y-m-d') : date('Y-m-d', strtotime('-7 days'));
        $endDate = date('Y-m-d');

        // Total medicines
        $totalMedicines = \App\Core\Database::query("SELECT COUNT(*) as count FROM medicines WHERE is_active = 1")->fetch(\PDO::FETCH_ASSOC)['count'];

        // Active batches
        $activeBatches = \App\Core\Database::query("SELECT COUNT(*) as count FROM batches WHERE status = 'active'")->fetch(\PDO::FETCH_ASSOC)['count'];

        // Total stock value
        $totalValue = \App\Core\Database::query("SELECT SUM(current_quantity * selling_price) as total FROM batches WHERE status = 'active'")->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;

        // Transactions
        $transactions = \App\Core\Database::query("SELECT COUNT(*) as count FROM stocks WHERE DATE(created_at) BETWEEN ? AND ?", [$startDate, $endDate])->fetch(\PDO::FETCH_ASSOC)['count'];

        // Stock in/out
        $stockIn = \App\Core\Database::query("SELECT COALESCE(SUM(quantity), 0) as total FROM stocks WHERE transaction_type = 'in' AND DATE(created_at) BETWEEN ? AND ?", [$startDate, $endDate])->fetch(\PDO::FETCH_ASSOC)['total'];
        $stockOut = \App\Core\Database::query("SELECT COALESCE(SUM(quantity), 0) as total FROM stocks WHERE transaction_type = 'out' AND DATE(created_at) BETWEEN ? AND ?", [$startDate, $endDate])->fetch(\PDO::FETCH_ASSOC)['total'];

        // Expiring batches
        $expiringBatches = $this->getExpiringBatches(30);

        // Low stock medicines
        $lowStockMedicines = $this->medicineModel->getLowStock((int)($_ENV['ALERT_LOW_STOCK_THRESHOLD'] ?? 10));

        // Top medicines
        $topMedicines = \App\Core\Database::query("
            SELECT m.name, COUNT(s.id) as transaction_count, SUM(s.quantity) as total_quantity
            FROM stocks s
            JOIN batches b ON b.id = s.batch_id
            JOIN medicines m ON m.id = b.medicine_id
            WHERE DATE(s.created_at) BETWEEN ? AND ?
            GROUP BY m.id
            ORDER BY transaction_count DESC
            LIMIT 5
        ", [$startDate, $endDate])->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'start_date' => date('M d, Y', strtotime($startDate)),
            'end_date' => date('M d, Y', strtotime($endDate)),
            'stats' => [
                'total_medicines' => $totalMedicines,
                'active_batches' => $activeBatches,
                'total_value' => $totalValue,
                'transactions' => $transactions,
                'stock_in' => $stockIn,
                'stock_out' => $stockOut
            ],
            'expiringBatches' => $expiringBatches,
            'lowStockMedicines' => $lowStockMedicines,
            'topMedicines' => $topMedicines
        ];
    }

    /**
     * Render email template
     */
    private function renderTemplate(string $template, array $data): string
    {
        extract($data);
        
        ob_start();
        include __DIR__ . "/../Views/emails/{$template}.php";
        $content = ob_get_clean();
        
        ob_start();
        $subject = $data['subject'] ?? 'Healthcare Supply Chain';
        include __DIR__ . "/../Views/emails/layout.php";
        return ob_get_clean();
    }

    /**
     * Test email configuration
     */
    public function testEmail(string $to): bool
    {
        return $this->mailer->test($to);
    }
}
