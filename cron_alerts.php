<?php

/**
 * Automated Alert Cron Job
 * Run this script daily via cron/task scheduler
 * 
 * Windows Task Scheduler: C:\xampp\php\php.exe "path\to\cron_alerts.php"
 * Linux Cron: 0 8 * * * /usr/bin/php /path/to/cron_alerts.php
 */

require_once __DIR__ . '/vendor/autoload.php';

load_env(__DIR__ . '/.env');

use App\Core\AlertService;

echo "=== Healthcare Supply Chain - Automated Alerts ===\n";
echo "Started at: " . date('Y-m-d H:i:s') . "\n\n";

$alertService = new AlertService();

// 1. Send Expiry Alerts
echo "1. Checking expiry alerts...\n";
$expiryResults = $alertService->sendExpiryAlerts();
echo "   Sent " . count($expiryResults) . " expiry alert(s)\n\n";

// 2. Send Low Stock Alerts
echo "2. Checking low stock alerts...\n";
$lowStockResults = $alertService->sendLowStockAlerts();
echo "   Sent " . count($lowStockResults) . " low stock alert(s)\n\n";

// 3. Send Daily Report (only on weekdays)
if (date('N') <= 5) { // Monday to Friday
    echo "3. Sending daily report...\n";
    $dailyResults = $alertService->sendDailyReport();
    echo "   Sent " . count($dailyResults) . " daily report(s)\n\n";
}

// 4. Send Weekly Report (only on Mondays)
if (date('N') == 1) {
    echo "4. Sending weekly report...\n";
    $weeklyResults = $alertService->sendWeeklyReport();
    echo "   Sent " . count($weeklyResults) . " weekly report(s)\n\n";
}

echo "Completed at: " . date('Y-m-d H:i:s') . "\n";
echo "=== End of Automated Alerts ===\n";
