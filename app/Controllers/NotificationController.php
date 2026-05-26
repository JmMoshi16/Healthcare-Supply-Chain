<?php

namespace App\Controllers;

use App\Core\Request;
use App\Models\Medicine;
use App\Models\Batch;
use App\Models\Notification;

class NotificationController extends BaseController
{
    public function index(Request $request)
    {
        header('Content-Type: application/json');

        $userId     = auth()['id'] ?? null;
        $notifModel = new Notification();
        $batch      = new Batch();
        $medicine   = new Medicine();

        $notifications = [];

        // ── 1. Persistent DB notifications (email-sent alerts, etc.) ──────
        $dbNotifs = $notifModel->getForUser($userId, 20);
        foreach ($dbNotifs as $n) {
            $notifications[] = [
                'id'      => 'db_' . $n['id'],
                'db_id'   => (int) $n['id'],
                'type'    => $n['type'],
                'urgency' => $n['urgency'],
                'icon'    => $n['icon'],
                'title'   => $n['title'],
                'message' => $n['message'],
                'link'    => $n['link'],
                'time'    => $this->timeAgo($n['created_at']),
                'unread'  => (bool) !$n['is_read'],
            ];
        }

        // ── 2. Live: medicines expiring within 30 days ────────────────────
        $expiring = $batch->getExpiringSoon(30);
        foreach ($expiring as $item) {
            $daysLeft = max(0, (int) floor((strtotime($item['expiry_date']) - time()) / 86400));
            $urgency  = $daysLeft <= 7 ? 'critical' : ($daysLeft <= 15 ? 'high' : 'medium');

            $notifications[] = [
                'id'      => 'exp_' . $item['id'],
                'db_id'   => null,
                'type'    => 'expiring',
                'urgency' => $urgency,
                'icon'    => 'clock-history',
                'title'   => 'Medicine Expiring Soon',
                'message' => "{$item['medicine_name']} (Batch: {$item['batch_number']}) expires in {$daysLeft} day" . ($daysLeft !== 1 ? 's' : ''),
                'link'    => "/batches/{$item['id']}",
                'time'    => $this->timeAgo($item['expiry_date']),
                'unread'  => true,
            ];
        }

        // ── 3. Live: low stock medicines ──────────────────────────────────
        $lowStock = $medicine->getLowStock(10);
        foreach ($lowStock as $item) {
            $stock   = (int) $item['total_stock'];
            $urgency = $stock === 0 ? 'critical' : ($stock <= 5 ? 'high' : 'medium');

            $notifications[] = [
                'id'      => 'low_' . $item['id'],
                'db_id'   => null,
                'type'    => 'low_stock',
                'urgency' => $urgency,
                'icon'    => 'arrow-down-circle',
                'title'   => $stock === 0 ? 'Out of Stock' : 'Low Stock Alert',
                'message' => "{$item['name']} has only {$stock} unit" . ($stock !== 1 ? 's' : '') . ' remaining',
                'link'    => "/medicines/{$item['id']}",
                'time'    => 'Just now',
                'unread'  => true,
            ];
        }

        // ── Sort: unread first, then by urgency ───────────────────────────
        $urgencyOrder = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];
        usort($notifications, function ($a, $b) use ($urgencyOrder) {
            if ($a['unread'] !== $b['unread']) {
                return $a['unread'] ? -1 : 1;
            }
            return ($urgencyOrder[$a['urgency']] ?? 3) - ($urgencyOrder[$b['urgency']] ?? 3);
        });

        $unreadCount = count(array_filter($notifications, fn($n) => $n['unread']));

        return $this->json([
            'success'        => true,
            'notifications'  => $notifications,
            'unread_count'   => $unreadCount,
        ]);
    }

    public function markAsRead(Request $request)
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $id   = $data['id'] ?? $request->input('id') ?? '';

        // Only mark DB notifications (prefixed with "db_")
        if (str_starts_with((string) $id, 'db_')) {
            $dbId = (int) substr($id, 3);
            if ($dbId > 0) {
                (new Notification())->markRead($dbId);
            }
        }

        return $this->json(['success' => true, 'message' => 'Notification marked as read']);
    }

    public function markAllAsRead(Request $request)
    {
        header('Content-Type: application/json');

        $userId = auth()['id'] ?? null;
        (new Notification())->markAllRead($userId);

        return $this->json(['success' => true, 'message' => 'All notifications marked as read']);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function timeAgo(string $datetime): string
    {
        $diff = time() - strtotime($datetime);

        if ($diff < 60)     return 'Just now';
        if ($diff < 3600)   return floor($diff / 60)   . ' min ago';
        if ($diff < 86400)  return floor($diff / 3600)  . ' hr ago';
        if ($diff < 604800) return floor($diff / 86400) . ' days ago';

        return date('M d, Y', strtotime($datetime));
    }
}
