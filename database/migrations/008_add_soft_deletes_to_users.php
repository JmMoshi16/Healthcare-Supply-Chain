<?php

return function (PDO $pdo): void {
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL");
    } catch (\PDOException $e) {
        if ($e->getCode() !== '42S21') throw $e;
    }
};
