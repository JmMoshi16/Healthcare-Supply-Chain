<?php

return function (PDO $pdo): void {
    $medicines = [
        // Active medicines
        ['Paracetamol', 'Acetaminophen', 'Analgesic', 'Pain reliever and fever reducer', 'tablet', 'Paracetamol.jpg', 1],
        ['Amoxicillin', 'Amoxicillin Trihydate', 'Antibiotic', 'Treats bacterial infections', 'capsule', 'Amoxicillin.jfif', 1],
        ['Ibuprofen', 'Ibuprofen', 'Anti-inflammatory', 'Reduces inflammation and pain', 'tablet', 'Ibuprofen', 1],
        ['Cetirizine', 'Cetirizine HCl', 'Antihistamine', 'Treats allergies and hay fever', 'tablet', 'Cetirizine.jpg', 1],
        ['Omeprazole', 'Omeprazole', 'Antacid', 'Reduces stomach acid production', 'capsule', 'Omeprazole', 1],
        ['Metformin', 'Metformin HCl', 'Antidiabetic', 'Controls blood sugar levels', 'tablet', 'Metformin', 1],
        ['Losartan', 'Losartan Potassium', 'Antihypertensive', 'Lowers blood pressure', 'tablet', 'Losartan.jpg', 1],
        ['Atorvastatin', 'Atorvastatin Calcium', 'Statin', 'Lowers cholesterol levels', 'tablet', 'Atorvastatin.jpeg', 1],
        ['Azithromycin', 'Azithromycin', 'Antibiotic', 'Treats respiratory infections', 'tablet', 'Azithromycin.jfif', 1],
        ['Salbutamol', 'Salbutamol Sulfate', 'Bronchodilator', 'Treats asthma and COPD', 'syrup', 'Salbutamol.jfif', 1],
        ['Diclofenac', 'Diclofenac Sodium', 'Anti-inflammatory', 'Pain and inflammation relief', 'tablet', 'Diclofenac', 1],
        ['Levofloxacin', 'Levofloxacin', 'Antibiotic', 'Treats bacterial infections', 'tablet', 'Levofloxacin', 1],
        ['Ranitidine', 'Ranitidine HCl', 'Antacid', 'Reduces stomach acid', 'tablet', 'Ranatidine.jfif', 1],
        ['Dexamethasone', 'Dexamethasone', 'Corticosteroid', 'Anti-inflammatory steroid', 'tablet', 'Dexamethasone', 1],
        ['Furosemide', 'Furosemide', 'Diuretic', 'Removes excess fluid', 'tablet', 'Furosemide', 1],
        
        // Inactive medicines
        ['Aspirin', 'Acetylsalicylic Acid', 'Analgesic', 'Pain reliever (discontinued)', 'tablet', 'Aspirin.jpg', 0],
        ['Codeine', 'Codeine Phosphate', 'Analgesic', 'Pain reliever (restricted)', 'tablet', 'Codeine.png', 0],
        ['Phenylephrine', 'Phenylephrine HCl', 'Decongestant', 'Nasal decongestant (seasonal)', 'syrup', 'Phenylephrine.jpg', 0],
        ['Chlorpheniramine', 'Chlorpheniramine Maleate', 'Antihistamine', 'Allergy relief (old formula)', 'tablet', 'Chlorpheniramine.jpg', 0],
        ['Tetracycline', 'Tetracycline HCl', 'Antibiotic', 'Antibiotic (replaced)', 'capsule', 'Tetracycline', 0],
    ];

    $stmt = $pdo->prepare("
        INSERT IGNORE INTO medicines (name, generic_name, category, description, unit, image, is_active)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($medicines as $medicine) {
        $stmt->execute($medicine);
    }

    $activeCount = count(array_filter($medicines, fn($m) => $m[6] === 1));
    $inactiveCount = count(array_filter($medicines, fn($m) => $m[6] === 0));
    
    echo "  ✓ Seeded " . count($medicines) . " medicines ({$activeCount} active, {$inactiveCount} inactive).\n";
};
