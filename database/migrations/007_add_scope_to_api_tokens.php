<?php

return function (PDO $pdo): void {
    $cols = array_column($pdo->query("SHOW COLUMNS FROM api_tokens")->fetchAll(), 'Field');
    if (!in_array('scope', $cols)) {
        $pdo->exec("ALTER TABLE api_tokens ADD COLUMN scope VARCHAR(255) NOT NULL DEFAULT 'read' AFTER expires_at");
    }
};
