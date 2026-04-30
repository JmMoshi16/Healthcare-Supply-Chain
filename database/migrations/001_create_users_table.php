<?php

/**
 * Migration 001 — Create users table
 *
 * Roles: superadmin | manager | staff
 * Registration is protected by verification codes (config/registration_codes.php)
 */
return function (PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id            INT          PRIMARY KEY AUTO_INCREMENT,
            fullname      VARCHAR(255) NOT NULL,
            email         VARCHAR(255) UNIQUE NOT NULL,
            password      VARCHAR(255) NOT NULL,
            role          ENUM('superadmin','manager','staff') NOT NULL DEFAULT 'staff',
            profile_image VARCHAR(255) NULL,
            is_active     TINYINT(1)   NOT NULL DEFAULT 1,
            created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
};
