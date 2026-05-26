<?php

/**
 * Migration 006 — Create notifications table
 *
 * Stores persistent notifications (email alerts, batch recalls, etc.)
 * NULL user_id = broadcast to all users
 */
return function (PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            type        VARCHAR(50)  NOT NULL,
            urgency     VARCHAR(20)  NOT NULL DEFAULT 'medium',
            icon        VARCHAR(50)  NOT NULL DEFAULT 'bell',
            title       VARCHAR(255) NOT NULL,
            message     TEXT         NOT NULL,
            link        VARCHAR(500) NOT NULL DEFAULT '/dashboard',
            is_read     TINYINT(1)   NOT NULL DEFAULT 0,
            user_id     INT UNSIGNED NULL COMMENT 'NULL = broadcast to all',
            created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_user_id   (user_id),
            INDEX idx_is_read   (is_read),
            INDEX idx_created   (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
};
