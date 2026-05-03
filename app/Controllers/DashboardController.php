<?php

namespace App\Controllers;

use App\Core\Request;
use App\Models\Medicine;
use App\Models\Batch;
use App\Models\Stock;
use App\Models\User;

class DashboardController extends BaseController
{
    public function index(Request $request)
    {
        $medicine = new Medicine();
        $batch    = new Batch();
        $stock    = new Stock();

        // Single aggregated query for medicine statistics
        $medicineStats = $medicine->getMedicineStats();
        
        // Single aggregated query for batch statistics with stock value
        $batchStats = $batch->getBatchStats();
        
        // Get expiring and low stock (already optimized with JOINs)
        $expiringSoon = $batch->getExpiringSoon(30);
        $lowStockItems = $medicine->getLowStock(10);
        
        // Single query for transaction statistics including today's count
        $transactionStats = $stock->getTransactionStats();
        $recentTransactions = $stock->getRecentTransactions(10);
        
        // Get expiring medicines for calendar with grouping in SQL
        $calendarExpirations = $batch->getExpiringGroupedByDate(
            date('Y-m-01'), 
            date('Y-m-t', strtotime('+3 months'))
        );

        $stats = [
            'total_medicines'      => $medicineStats['total'] ?? 0,
            'active_medicines'     => $medicineStats['active'] ?? 0,
            'inactive_medicines'   => $medicineStats['inactive'] ?? 0,
            'total_batches'        => $batchStats['total'] ?? 0,
            'active_batches'       => $batchStats['active'] ?? 0,
            'expired_batches'      => $batchStats['expired'] ?? 0,
            'total_stock_value'    => $batchStats['total_value'] ?? 0,
            'expiring_soon'        => $expiringSoon,
            'expiring_count'       => count($expiringSoon),
            'low_stock'            => $lowStockItems,
            'low_stock_count'      => count($lowStockItems),
            'recent_transactions'  => $recentTransactions,
            'today_transactions'   => $transactionStats['today_count'] ?? 0,
            'calendar_expirations' => $calendarExpirations,
            'weekly_stats'         => $stock->getWeeklyStats(),
            'monthly_stats'        => $stock->getMonthlyStats(),
        ];

        if (has_role('superadmin')) {
            $stats['total_users'] = (new User())->getUserCount();
        }

        return $this->view('dashboard/index', ['stats' => $stats]);
    }
}
