<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Response;

class ExpiryCalendarController extends BaseController
{
    public function getExpiringByMonth()
    {
        $month = $_GET['month'] ?? date('Y-m');
        
        $sql = "SELECT 
                    b.expiry_date,
                    m.name as medicine_name,
                    m.category,
                    b.batch_number,
                    b.current_quantity,
                    m.id as medicine_id,
                    DATEDIFF(b.expiry_date, CURDATE()) as days_until_expiry
                FROM batches b
                INNER JOIN medicines m ON b.medicine_id = m.id
                WHERE DATE_FORMAT(b.expiry_date, '%Y-%m') = ?
                AND b.current_quantity > 0
                ORDER BY b.expiry_date ASC";
        
        $stmt = Database::query($sql, [$month]);
        $expirations = $stmt->fetchAll();
        
        // Group by date
        $grouped = [];
        foreach ($expirations as $exp) {
            $date = $exp['expiry_date'];
            if (!isset($grouped[$date])) {
                $grouped[$date] = [];
            }
            $grouped[$date][] = $exp;
        }
        
        Response::json([
            'success' => true,
            'expirations' => $grouped
        ]);
    }
}
