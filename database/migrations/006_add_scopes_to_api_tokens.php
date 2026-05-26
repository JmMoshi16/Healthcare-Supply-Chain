<?php

return function (PDO $pdo): void {
    // Add new columns to api_tokens table
    $pdo->exec("
        ALTER TABLE api_tokens
        ADD COLUMN scopes TEXT NULL COMMENT 'JSON array of permission scopes',
        ADD COLUMN name VARCHAR(255) NULL COMMENT 'Token name/description',
        ADD COLUMN last_used_at TIMESTAMP NULL COMMENT 'Last time token was used',
        ADD COLUMN last_used_ip VARCHAR(45) NULL COMMENT 'Last IP address that used token',
        ADD COLUMN is_revoked TINYINT(1) DEFAULT 0 COMMENT 'Whether token is revoked',
        ADD COLUMN revoked_at TIMESTAMP NULL COMMENT 'When token was revoked',
        ADD COLUMN revoked_by INT NULL COMMENT 'User who revoked the token',
        ADD INDEX idx_is_revoked (is_revoked),
        ADD INDEX idx_user_id (user_id)
    ");
};
