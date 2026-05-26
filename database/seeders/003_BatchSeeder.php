<?php

return function (PDO $pdo): void {
    $batches = [
        [1, 'PCM-2024-001', '2024-01-15', '2026-01-15', 'PharmaCorp Ltd', 2.50, 5.00, 1000],
        [1, 'PCM-2024-002', '2024-06-20', '2026-06-20', 'PharmaCorp Ltd', 2.50, 5.00, 500],
        [2, 'AMX-2024-001', '2024-03-10', '2025-03-10', 'MediSupply Inc', 8.00, 15.00, 300],
        [2, 'AMX-2024-002', '2024-07-15', '2025-07-15', 'MediSupply Inc', 8.00, 15.00, 200],
        [3, 'IBU-2024-001', '2024-02-20', '2026-02-20', 'HealthPharma', 3.00, 6.50, 800],
        [4, 'CET-2024-001', '2024-04-05', '2025-12-05', 'AllergyMeds Co', 4.50, 9.00, 400],
        [5, 'OMP-2024-001', '2024-05-12', '2026-05-12', 'GastroCare Ltd', 12.00, 22.00, 250],
    ];

    $stmt = $pdo->prepare("
        INSERT IGNORE INTO batches 
        (medicine_id, batch_number, manufacturing_date, expiry_date, supplier, purchase_price, selling_price, initial_quantity, current_quantity, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
    ");

    foreach ($batches as $batch) {
        $stmt->execute([...$batch, $batch[7]]);
    }

    echo "  ✓ Seeded " . count($batches) . " medicine batches.\n";
};
