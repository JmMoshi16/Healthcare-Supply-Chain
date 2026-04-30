<?php

return function (PDO $pdo): void {
    $transactions = [
        [1, 'in', 1000, 'Initial stock purchase', 1],
        [1, 'out', 150, 'Pharmacy dispensing', 1],
        [2, 'in', 500, 'Restock order', 1],
        [3, 'in', 300, 'Initial stock purchase', 1],
        [3, 'out', 50, 'Hospital order', 1],
        [4, 'in', 200, 'Initial stock purchase', 1],
        [5, 'in', 800, 'Initial stock purchase', 1],
        [5, 'out', 200, 'Bulk pharmacy order', 1],
        [6, 'in', 400, 'Initial stock purchase', 1],
        [7, 'in', 250, 'Initial stock purchase', 1],
    ];

    $stmt = $pdo->prepare("
        INSERT INTO stocks (batch_id, transaction_type, quantity, reason, performed_by)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($transactions as $transaction) {
        $stmt->execute($transaction);
    }

    echo "  ✓ Seeded " . count($transactions) . " stock transactions.\n";
};
