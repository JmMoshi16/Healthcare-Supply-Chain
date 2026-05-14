<?php

return function (PDO $pdo): void {
    try {
        $pdo->exec("ALTER TABLE medicines ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL");
    } catch (\PDOException $e) {
        if ($e->getCode() !== '42S21') throw $e;
    }
    try {
        $pdo->exec("ALTER TABLE batches ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL");
    } catch (\PDOException $e) {
        if ($e->getCode() !== '42S21') throw $e;
    }
};
