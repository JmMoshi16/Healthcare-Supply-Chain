<?php

namespace App\Controllers;

use App\Core\Request;
use App\Models\Medicine;
use App\Models\Batch;

class NotificationController extends BaseController
{
    public function index(Request $request)
    {
        $batch = new Batch();
        $medicine = new Medicine();
        
        $notifications = [];
        
        // Expiring medicines (30 days)
        $expiring = $batch->getExpiringSoon(30);
        foreach ($expiring as $item) {
            $daysLeft = max(0, floor((strtotime($item['expiry_date']) - time()) / 86400));
            $urgency = $daysLeft <= 7 ? 'critical' : ($daysLeft <= 15 ? 'high' : 'medium');
            
            $notifications[] = [
                'id' => 'exp_' . $item['id'],
                'type' => 'expiring',
                'urgency' => $urgency,
                'icon' => 'clock-history',
                'title' => 'Medicine Expiring Soon',
                'message' => "{$item['medicine_name']} (Batch: {$item['batch_number']}) expires in {$daysLeft} days",
                'link' => "/batches/{$item['id']}",
                'time' => $this->timeAgo($item['expiry_date']),
                'unread' => true
            ];
        }
        
        // Low stock medicines
        $lowStock = $medicine->getLowStock(10);
        foreach ($lowStock as $item) {
            $stock = (int)$item['total_stock'];
            $urgency = $stock == 0 ? 'critical' : ($stock <= 5 ? 'high' : 'medium');
            
            $notifications[] = [
                'id' => 'low_' . $item['id'],
                'type' => 'low_stock',
                'urgency' => $urgency,
                'icon' => 'arrow-down-circle',
                'title' => $stock == 0 ? 'Out of Stock' : 'Low Stock Alert',
                'message' => "{$item['name']} has only {$stock} units remaining",
                'link' => "/medicines/{$item['id']}",
                'time' => 'Just now',
                'unread' => true
            ];
        }
        
        // Sort by urgency
        usort($notifications, function($a, $b) {
            $order = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];
            return $order[$a['urgency']] - $order[$b['urgency']];
        });
        
        return $this->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => count($notifications)
        ]);
    }
    
    public function markAsRead(Request $request)
    {
        $id = $request->input('id');
        
        // In a real app, you'd update the database here
        // For now, we'll just return success
        
        return $this->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }
    
    public function markAllAsRead(Request $request)
    {
        // In a real app, you'd update all notifications in the database
        
        return $this->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }
    
    private function timeAgo($datetime)
    {
        $time = strtotime($datetime);
        $diff = time() - $time;
        
        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . ' min ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
        if ($diff < 604800) return floor($diff / 86400) . ' days ago';
        
        return date('M d, Y', $time);
    }
}
