<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\AlertService;

class EmailController extends BaseController
{
    private AlertService $alertService;

    public function __construct()
    {
        $this->alertService = new AlertService();
    }

    /**
     * Email management dashboard
     */
    public function index(Request $request)
    {
        if (!can('users.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/dashboard');
        }

        return $this->view('emails/index');
    }

    /**
     * Test email configuration  POST /emails/test
     */
    public function test(Request $request)
    {
        if (!can('users.*')) {
            return $this->json(['success' => false, 'message' => 'Access denied'], 403);
        }

        $email = $request->post('email') ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['success' => false, 'message' => 'Invalid email address']);
        }

        $result = $this->alertService->testEmail($email);

        if ($result) {
            return $this->json([
                'success' => true,
                'message' => "Test email sent to {$email}! Check your inbox.",
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Failed to send test email. Check your SMTP settings in .env',
        ]);
    }

    /**
     * Manually trigger expiry alerts  POST /emails/send-expiry-alerts
     */
    public function sendExpiryAlerts(Request $request)
    {
        if (!can('users.*')) {
            return $this->json(['success' => false, 'message' => 'Access denied'], 403);
        }

        $results      = $this->alertService->sendExpiryAlerts();
        $successCount = count(array_filter($results, fn($r) => $r['success']));
        $total        = count($results);

        if ($total === 0) {
            return $this->json([
                'success' => true,
                'message' => 'No expiring batches found — no alerts needed.',
                'results' => [],
            ]);
        }

        return $this->json([
            'success' => true,
            'message' => "Sent {$successCount}/{$total} expiry alert(s). Check the notification bell!",
            'results' => $results,
        ]);
    }

    /**
     * Manually trigger low stock alerts  POST /emails/send-low-stock-alerts
     */
    public function sendLowStockAlerts(Request $request)
    {
        if (!can('users.*')) {
            return $this->json(['success' => false, 'message' => 'Access denied'], 403);
        }

        $results      = $this->alertService->sendLowStockAlerts();
        $successCount = count(array_filter($results, fn($r) => $r['success']));
        $total        = count($results);

        if ($total === 0) {
            return $this->json([
                'success' => true,
                'message' => 'All stock levels are healthy — no alerts needed.',
                'results' => [],
            ]);
        }

        return $this->json([
            'success' => true,
            'message' => "Sent {$successCount}/{$total} low stock alert(s). Check the notification bell!",
            'results' => $results,
        ]);
    }

    /**
     * Send daily report  POST /emails/send-daily-report
     */
    public function sendDailyReport(Request $request)
    {
        if (!can('users.*')) {
            return $this->json(['success' => false, 'message' => 'Access denied'], 403);
        }

        $results      = $this->alertService->sendDailyReport();
        $successCount = count(array_filter($results, fn($r) => $r['success']));
        $total        = count($results);

        return $this->json([
            'success' => true,
            'message' => "Daily report sent to {$successCount}/{$total} recipient(s). Check the notification bell!",
            'results' => $results,
        ]);
    }

    /**
     * Send weekly report  POST /emails/send-weekly-report
     */
    public function sendWeeklyReport(Request $request)
    {
        if (!can('users.*')) {
            return $this->json(['success' => false, 'message' => 'Access denied'], 403);
        }

        $results      = $this->alertService->sendWeeklyReport();
        $successCount = count(array_filter($results, fn($r) => $r['success']));
        $total        = count($results);

        return $this->json([
            'success' => true,
            'message' => "Weekly report sent to {$successCount}/{$total} recipient(s). Check the notification bell!",
            'results' => $results,
        ]);
    }
}
