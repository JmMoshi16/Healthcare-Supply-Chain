<?php

namespace App\Controllers;

use App\Core\Request;
use App\Models\Batch;

class ExpiryCalendarController extends BaseController
{
    public function getExpiringByMonth(Request $request)
    {
        $month = $request->get('month'); // Format: YYYY-MM
        
        if (!$month || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            return $this->json(['success' => false, 'message' => 'Invalid month format'], 400);
        }

        $batch = new Batch();
        $expirations = $batch->getExpiringByDateRange(
            $month . '-01',
            date('Y-m-t', strtotime($month . '-01'))
        );

        // Group by expiry date
        $grouped = [];
        foreach ($expirations as $item) {
            $date = $item['expiry_date'];
            if (!isset($grouped[$date])) {
                $grouped[$date] = [];
            }
            $grouped[$date][] = $item;
        }

        return $this->json([
            'success' => true,
            'expirations' => $grouped,
        ]);
    }
}
