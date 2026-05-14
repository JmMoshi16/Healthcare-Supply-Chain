<?php

return function (PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS activity_logs (
            id         INT          PRIMARY KEY AUTO_INCREMENT,
            user_id    INT          NULL,
            action     VARCHAR(50)  NOT NULL,
            model      VARCHAR(100) NOT NULL,
            model_id   INT          NOT NULL,
            old_data   JSON         NULL,
            new_data   JSON         NULL,
            ip_address VARCHAR(45)  NULL,
            timestamp  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
};
