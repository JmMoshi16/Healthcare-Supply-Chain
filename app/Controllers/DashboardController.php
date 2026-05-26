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

        // Get all medicines with active/inactive counts
        $allMedicines = $medicine->all();
        $activeMedicines = array_filter($allMedicines, fn($m) => $m['is_active'] == 1);
        $inactiveMedicines = array_filter($allMedicines, fn($m) => $m['is_active'] == 0);
        
        // Get batch statistics
        $allBatches = $batch->all();
        $activeBatches = array_filter($allBatches, fn($b) => $b['status'] === 'active');
        $expiredBatches = array_filter($allBatches, fn($b) => $b['status'] === 'expired');
        
        // Get expiring and low stock
        $expiringSoon = $batch->getExpiringSoon(30);
        $lowStockItems = $medicine->getLowStock();
        
        // Get transaction statistics
        $recentTransactions = $stock->getRecentTransactions(10);
        $todayTransactions = array_filter($recentTransactions, function($t) {
            return date('Y-m-d', strtotime($t['created_at'])) === date('Y-m-d');
        });
        
        // Calculate total stock value
        $totalStockValue = 0;
        foreach ($allBatches as $b) {
            $totalStockValue += ($b['current_quantity'] * $b['selling_price']);
        }
        
        // Get expiring medicines for calendar (next 3 months)
        $calendarExpirations = $batch->getExpiringByDateRange(date('Y-m-01'), date('Y-m-t', strtotime('+3 months')));
        
        // Group by expiry date
        $groupedExpirations = [];
        foreach ($calendarExpirations as $item) {
            $date = $item['expiry_date'];
            if (!isset($groupedExpirations[$date])) {
                $groupedExpirations[$date] = [];
            }
            $groupedExpirations[$date][] = $item;
        }

        $stats = [
            'all_medicines'       => $allMedicines,
            'total_medicines'     => count($allMedicines),
            'active_medicines'    => count($activeMedicines),
            'inactive_medicines'  => count($inactiveMedicines),
            'total_batches'       => count($allBatches),
            'active_batches'      => count($activeBatches),
            'expired_batches'     => count($expiredBatches),
            'expiring_soon'       => $expiringSoon,
            'expiring_count'      => count($expiringSoon),
            'low_stock'           => $lowStockItems,
            'low_stock_count'     => count($lowStockItems),
            'recent_transactions' => $recentTransactions,
            'today_transactions'  => count($todayTransactions),
            'total_stock_value'   => $totalStockValue,
            'calendar_expirations' => $groupedExpirations,
            'weekly_stats'        => $stock->getWeeklyStats(),
            'monthly_stats'       => $stock->getMonthlyStats(),
        ];

        if (has_role('superadmin')) {
            $stats['total_users'] = count((new User())->all());
        }

        return $this->view('dashboard/index', ['stats' => $stats]);
    }
}
