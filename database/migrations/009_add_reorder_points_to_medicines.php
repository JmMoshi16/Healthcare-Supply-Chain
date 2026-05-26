<?php

return function (PDO $pdo): void {
    // Check if column exists first to be safe
    $stmt = $pdo->query("SHOW COLUMNS FROM medicines LIKE 'minimum_stock'");
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $pdo->exec("
            ALTER TABLE medicines 
            ADD COLUMN minimum_stock INT NOT NULL DEFAULT 10,
            ADD COLUMN reorder_quantity INT NOT NULL DEFAULT 50
        ");
    }
};
