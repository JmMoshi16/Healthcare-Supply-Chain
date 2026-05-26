<?php

return function (PDO $pdo): void {
    // Resolve batch IDs dynamically by batch_number
    $batchMap = $pdo->query('SELECT id, batch_number FROM batches')
        ->fetchAll(PDO::FETCH_KEY_PAIR);
    $batchMap = array_flip($batchMap);

    $transactions = [
        ['PCM-2024-001', 'in',  1000, 'Initial stock purchase', 1],
        ['PCM-2024-001', 'out',  150, 'Pharmacy dispensing',    1],
        ['PCM-2024-002', 'in',   500, 'Restock order',          1],
        ['AMX-2024-001', 'in',   300, 'Initial stock purchase', 1],
        ['AMX-2024-001', 'out',   50, 'Hospital order',         1],
        ['AMX-2024-002', 'in',   200, 'Initial stock purchase', 1],
        ['IBU-2024-001', 'in',   800, 'Initial stock purchase', 1],
        ['IBU-2024-001', 'out',  200, 'Bulk pharmacy order',    1],
        ['CET-2024-001', 'in',   400, 'Initial stock purchase', 1],
        ['OMP-2024-001', 'in',   250, 'Initial stock purchase', 1],
    ];

    $stmt = $pdo->prepare("
        INSERT INTO stocks (batch_id, transaction_type, quantity, reason, performed_by)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($transactions as [$batchNumber, $type, $qty, $reason, $by]) {
        if (!isset($batchMap[$batchNumber])) continue;
        $stmt->execute([$batchMap[$batchNumber], $type, $qty, $reason, $by]);
    }

    echo "  ✓ Seeded " . count($transactions) . " stock transactions.\n";
};
