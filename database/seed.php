<?php

require_once __DIR__ . '/../vendor/autoload.php';

load_env(__DIR__ . '/../.env');

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $_ENV['DB_HOST'], $_ENV['DB_PORT'] ?? '3306', $_ENV['DB_NAME']),
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$seeders = [
    __DIR__ . '/seeders/UserSeeder.php',
    __DIR__ . '/seeders/CategorySeeder.php',
    __DIR__ . '/seeders/MedicineSeeder.php',
    __DIR__ . '/seeders/BatchSeeder.php',
    __DIR__ . '/seeders/StockSeeder.php',
];

foreach ($seeders as $file) {
    if (file_exists($file)) {
        $seeder = require $file;
        $seeder($pdo);
        echo "Seeded: " . basename($file) . PHP_EOL;
    } else {
        echo "Warning: Seeder file not found: " . basename($file) . PHP_EOL;
    }
}

echo "All seeders completed." . PHP_EOL;

