<?php

return function (PDO $pdo): void {
    $categories = [
        ['Analgesics'],
        ['Antibiotics'],
        ['Vitamins & Supplements'],
        ['Antihistamines'],
        ['Cardiovascular Drugs'],
        ['Gastrointestinal Drugs']
    ];

    $stmt = $pdo->prepare("
        INSERT IGNORE INTO categories (name)
        VALUES (?)
    ");

    foreach ($categories as $category) {
        $stmt->execute($category);
    }

    echo "  ✓ Seeded " . count($categories) . " medicine categories.\n";
};