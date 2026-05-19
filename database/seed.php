<?php

require_once __DIR__ . '/../vendor/autoload.php';

load_env(__DIR__ . '/../.env');

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $_ENV['DB_HOST'], $_ENV['DB_PORT'] ?? '3306', $_ENV['DB_NAME']),
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$order = ['UserSeeder.php', 'MedicineSeeder.php', 'BatchSeeder.php', 'StockSeeder.php'];
$seeders = array_map(fn($f) => __DIR__ . '/seeders/' . $f, $order);

foreach ($seeders as $file) {
    $seeder = require $file;
    $seeder($pdo);
    echo "Seeded: " . basename($file) . PHP_EOL;
}

echo "All seeders completed." . PHP_EOL;
