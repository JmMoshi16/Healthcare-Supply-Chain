<?php

return function (PDO $pdo): void {
    $medicines = [
        // Active medicines
        ['Paracetamol', 'Acetaminophen', 'Analgesic', 'Pain reliever and fever reducer', 'tablet', 1],
        ['Amoxicillin', 'Amoxicillin Trihydate', 'Antibiotic', 'Treats bacterial infections', 'capsule', 1],
        ['Ibuprofen', 'Ibuprofen', 'Anti-inflammatory', 'Reduces inflammation and pain', 'tablet', 1],
        ['Cetirizine', 'Cetirizine HCl', 'Antihistamine', 'Treats allergies and hay fever', 'tablet', 1],
        ['Omeprazole', 'Omeprazole', 'Antacid', 'Reduces stomach acid production', 'capsule', 1],
        ['Metformin', 'Metformin HCl', 'Antidiabetic', 'Controls blood sugar levels', 'tablet', 1],
        ['Losartan', 'Losartan Potassium', 'Antihypertensive', 'Lowers blood pressure', 'tablet', 1],
        ['Atorvastatin', 'Atorvastatin Calcium', 'Statin', 'Lowers cholesterol levels', 'tablet', 1],
        ['Azithromycin', 'Azithromycin', 'Antibiotic', 'Treats respiratory infections', 'tablet', 1],
        ['Salbutamol', 'Salbutamol Sulfate', 'Bronchodilator', 'Treats asthma and COPD', 'syrup', 1],
        ['Diclofenac', 'Diclofenac Sodium', 'Anti-inflammatory', 'Pain and inflammation relief', 'tablet', 1],
        ['Levofloxacin', 'Levofloxacin', 'Antibiotic', 'Treats bacterial infections', 'tablet', 1],
        ['Ranitidine', 'Ranitidine HCl', 'Antacid', 'Reduces stomach acid', 'tablet', 1],
        ['Dexamethasone', 'Dexamethasone', 'Corticosteroid', 'Anti-inflammatory steroid', 'tablet', 1],
        ['Furosemide', 'Furosemide', 'Diuretic', 'Removes excess fluid', 'tablet', 1],
        
        // Inactive medicines
        ['Aspirin', 'Acetylsalicylic Acid', 'Analgesic', 'Pain reliever (discontinued)', 'tablet', 0],
        ['Codeine', 'Codeine Phosphate', 'Analgesic', 'Pain reliever (restricted)', 'tablet', 0],
        ['Phenylephrine', 'Phenylephrine HCl', 'Decongestant', 'Nasal decongestant (seasonal)', 'syrup', 0],
        ['Chlorpheniramine', 'Chlorpheniramine Maleate', 'Antihistamine', 'Allergy relief (old formula)', 'tablet', 0],
        ['Tetracycline', 'Tetracycline HCl', 'Antibiotic', 'Antibiotic (replaced)', 'capsule', 0],
    ];

    // Helper to resolve category names to seeded categories
    $resolveCategoryName = function (string $name): string {
        $map = [
            'analgesic' => 'Analgesics',
            'antibiotic' => 'Antibiotics',
            'antihistamine' => 'Antihistamines',
            'antacid' => 'Gastrointestinal Drugs',
            'anti-inflammatory' => 'Analgesics',
            'antidiabetic' => 'Gastrointestinal Drugs',
            'antihypertensive' => 'Cardiovascular Drugs',
            'statin' => 'Cardiovascular Drugs',
            'bronchodilator' => 'Vitamins & Supplements',
            'corticosteroid' => 'Vitamins & Supplements',
            'diuretic' => 'Cardiovascular Drugs',
            'decongestant' => 'Antihistamines',
        ];
        $lower = strtolower($name);
        return $map[$lower] ?? ucwords($name);
    };

    // Load category mapping from database
    $catStmt = $pdo->query("SELECT id, name FROM categories");
    $categories = $catStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $categoryMap = [];
    foreach ($categories as $id => $name) {
        $categoryMap[strtolower($name)] = $id;
    }

    $stmt = $pdo->prepare("
        INSERT IGNORE INTO medicines (name, generic_name, category_id, description, unit, is_active)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($medicines as $medicine) {
        $resolvedName = $resolveCategoryName($medicine[2]);
        $resolvedNameLower = strtolower($resolvedName);

        // If category doesn't exist in the table, insert it dynamically
        if (!isset($categoryMap[$resolvedNameLower])) {
            $insertCat = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $insertCat->execute([$resolvedName]);
            $categoryId = $pdo->lastInsertId();
            $categoryMap[$resolvedNameLower] = $categoryId;
        } else {
            $categoryId = $categoryMap[$resolvedNameLower];
        }

        $stmt->execute([
            $medicine[0], // name
            $medicine[1], // generic_name
            $categoryId,  // category_id
            $medicine[3], // description
            $medicine[4], // unit
            $medicine[5]  // is_active
        ]);
    }

    $activeCount = count(array_filter($medicines, fn($m) => $m[5] === 1));
    $inactiveCount = count(array_filter($medicines, fn($m) => $m[5] === 0));
    
    echo "  ✓ Seeded " . count($medicines) . " medicines ({$activeCount} active, {$inactiveCount} inactive).\n";
};

