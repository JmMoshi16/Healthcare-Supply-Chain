<?php

return function (PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS stocks (
            id INT PRIMARY KEY AUTO_INCREMENT,
            batch_id INT NOT NULL,
            transaction_type ENUM('in','out','adjustment') NOT NULL,
            quantity INT NOT NULL,
            reason VARCHAR(255),
            performed_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE,
            FOREIGN KEY (performed_by) REFERENCES users(id),
            INDEX idx_transaction_type (transaction_type),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
};
