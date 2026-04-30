<?php

return function (PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS batches (
            id INT PRIMARY KEY AUTO_INCREMENT,
            medicine_id INT NOT NULL,
            batch_number VARCHAR(50) UNIQUE NOT NULL,
            manufacturing_date DATE NOT NULL,
            expiry_date DATE NOT NULL,
            supplier VARCHAR(255),
            purchase_price DECIMAL(10,2) NOT NULL,
            selling_price DECIMAL(10,2) NOT NULL,
            initial_quantity INT NOT NULL,
            current_quantity INT NOT NULL,
            status ENUM('active','expired','recalled') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE,
            INDEX idx_expiry (expiry_date),
            INDEX idx_batch_number (batch_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
};
