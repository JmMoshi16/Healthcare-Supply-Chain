<?php

/**
 * Migration 006 — Create categories table
 */
return function (PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id         INT          PRIMARY KEY AUTO_INCREMENT,
            name       VARCHAR(255) UNIQUE NOT NULL,
            created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
};