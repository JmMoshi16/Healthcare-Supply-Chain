<?php

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

// Load environment
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

echo "=== NOTIFICATION DEBUG TOOL ===\n\n";

try {
    // Check expiring medicines
    echo "1. Checking Expiring Medicines (30 days)...\n";
    $sql = "
        SELECT b.*, m.name AS medicine_name, 
               DATEDIFF(b.expiry_date, CURDATE()) AS days_left
        FROM batches b
        JOIN medicines m ON m.id = b.medicine_id
        WHERE b.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
          AND b.status = 'active'
          AND b.current_quantity > 0
        ORDER BY b.expiry_date ASC
    ";
    
    $expiring = Database::query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($expiring)) {
        echo "   ❌ No medicines expiring in the next 30 days\n";
        echo "   💡 TIP: Add batches with expiry dates within 30 days\n\n";
    } else {
        echo "   ✅ Found " . count($expiring) . " expiring medicine(s):\n";
        foreach ($expiring as $item) {
            echo "      - {$item['medicine_name']} (Batch: {$item['batch_number']}) - {$item['days_left']} days left\n";
        }
        echo "\n";
    }
    
    // Check low stock
    echo "2. Checking Low Stock Medicines (< 10 units)...\n";
    $sql = "
        SELECT m.*, COALESCE(SUM(b.current_quantity), 0) AS total_stock
        FROM medicines m
        LEFT JOIN batches b ON b.medicine_id = m.id AND b.status = 'active'
        WHERE m.is_active = 1
        GROUP BY m.id
        HAVING total_stock < 10
        ORDER BY total_stock ASC
    ";
    
    $lowStock = Database::query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($lowStock)) {
        echo "   ❌ No medicines with low stock\n";
        echo "   💡 TIP: All medicines have sufficient stock (≥ 10 units)\n\n";
    } else {
        echo "   ✅ Found " . count($lowStock) . " low stock medicine(s):\n";
        foreach ($lowStock as $item) {
            $stock = (int)$item['total_stock'];
            $status = $stock == 0 ? '🔴 OUT OF STOCK' : ($stock <= 5 ? '🟠 VERY LOW' : '🟡 LOW');
            echo "      - {$item['name']} - {$stock} units {$status}\n";
        }
        echo "\n";
    }
    
    // Total notifications
    $totalNotifications = count($expiring) + count($lowStock);
    
    echo "=== SUMMARY ===\n";
    echo "Total Notifications: {$totalNotifications}\n";
    
    if ($totalNotifications == 0) {
        echo "\n❌ NO NOTIFICATIONS AVAILABLE\n\n";
        echo "To see notifications, you need to:\n";
        echo "1. Add batches with expiry dates within 30 days, OR\n";
        echo "2. Reduce stock levels below 10 units\n\n";
        echo "Quick Fix:\n";
        echo "- Go to Batches page\n";
        echo "- Edit an existing batch\n";
        echo "- Set expiry date to: " . date('Y-m-d', strtotime('+15 days')) . " (15 days from now)\n";
        echo "- Save and refresh notifications\n";
    } else {
        echo "\n✅ NOTIFICATIONS ARE WORKING!\n";
        echo "You should see {$totalNotifications} notification(s) in the bell icon.\n\n";
        echo "If you don't see them:\n";
        echo "1. Hard refresh browser (Ctrl+Shift+R)\n";
        echo "2. Clear browser cache\n";
        echo "3. Check browser console (F12) for errors\n";
        echo "4. Verify you're logged in\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nDatabase connection failed. Check your .env file:\n";
    echo "- DB_HOST=" . ($_ENV['DB_HOST'] ?? 'not set') . "\n";
    echo "- DB_NAME=" . ($_ENV['DB_NAME'] ?? 'not set') . "\n";
    echo "- DB_USER=" . ($_ENV['DB_USER'] ?? 'not set') . "\n";
}

echo "\n";
