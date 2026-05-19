<?php

return function (PDO $pdo): void {
    $pdo->exec("
        ALTER TABLE stocks
        ADD COLUMN recipient VARCHAR(255) AFTER reason,
        ADD COLUMN ward_department VARCHAR(100) AFTER recipient,
        ADD COLUMN reason_code ENUM('purchase','dispensing','return','damaged','expired','adjustment','transfer') AFTER transaction_type,
        ADD COLUMN reference_number VARCHAR(50) AFTER reason_code,
        ADD INDEX idx_reason_code (reason_code),
        ADD INDEX idx_ward (ward_department)
    ");
};
