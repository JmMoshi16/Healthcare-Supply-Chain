<?php
// Test API endpoint
require __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

$_GET['month'] = '2026-05';
$month = $_GET['month'];

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

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'expirations' => $grouped,
    'debug' => [
        'month' => $month,
        'total_records' => count($expirations),
        'dates' => array_keys($grouped)
    ]
], JSON_PRETTY_PRINT);
