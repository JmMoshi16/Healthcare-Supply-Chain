<?php

return function (PDO $pdo): void {
    // Fetch existing columns to make this migration idempotent
    $existing = array_column(
        $pdo->query("SHOW COLUMNS FROM api_tokens")->fetchAll(PDO::FETCH_ASSOC),
        'Field'
    );

    $columnsToAdd = [];
    if (!in_array('scopes', $existing)) {
        $columnsToAdd[] = "ADD COLUMN scopes TEXT NULL COMMENT 'JSON array of permission scopes'";
    }
    if (!in_array('name', $existing)) {
        $columnsToAdd[] = "ADD COLUMN name VARCHAR(255) NULL COMMENT 'Token name/description'";
    }
    if (!in_array('last_used_at', $existing)) {
        $columnsToAdd[] = "ADD COLUMN last_used_at TIMESTAMP NULL COMMENT 'Last time token was used'";
    }
    if (!in_array('last_used_ip', $existing)) {
        $columnsToAdd[] = "ADD COLUMN last_used_ip VARCHAR(45) NULL COMMENT 'Last IP address that used token'";
    }
    if (!in_array('is_revoked', $existing)) {
        $columnsToAdd[] = "ADD COLUMN is_revoked TINYINT(1) DEFAULT 0 COMMENT 'Whether token is revoked'";
    }
    if (!in_array('revoked_at', $existing)) {
        $columnsToAdd[] = "ADD COLUMN revoked_at TIMESTAMP NULL COMMENT 'When token was revoked'";
    }
    if (!in_array('revoked_by', $existing)) {
        $columnsToAdd[] = "ADD COLUMN revoked_by INT NULL COMMENT 'User who revoked the token'";
    }

    if (!empty($columnsToAdd)) {
        // Also add indexes only when columns are being added fresh
        $indexes = [];
        if (in_array('ADD COLUMN is_revoked TINYINT(1) DEFAULT 0 COMMENT \'Whether token is revoked\'', $columnsToAdd)) {
            $indexes[] = "ADD INDEX idx_is_revoked (is_revoked)";
        }
        // idx_user_id may already exist from the original CREATE TABLE
        $existingIndexes = array_column(
            $pdo->query("SHOW INDEX FROM api_tokens")->fetchAll(PDO::FETCH_ASSOC),
            'Key_name'
        );
        if (!in_array('idx_user_id', $existingIndexes)) {
            $indexes[] = "ADD INDEX idx_user_id (user_id)";
        }

        $alterParts = array_merge($columnsToAdd, $indexes);
        $pdo->exec("ALTER TABLE api_tokens " . implode(", ", $alterParts));
    }
};
