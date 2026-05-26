<?php

/**
 * Migration 007 — Link medicines table to categories table
 */
return function (PDO $pdo): void {
    // 1. Drop the old text index and column
    $pdo->exec("ALTER TABLE medicines DROP INDEX idx_category");
    $pdo->exec("ALTER TABLE medicines DROP COLUMN category");

    // 2. Add the new category_id integer column
    $pdo->exec("ALTER TABLE medicines ADD COLUMN category_id INT AFTER generic_name");

    // 3. Setup the Foreign Key constraint linking them together
    $pdo->exec("ALTER TABLE medicines 
        ADD CONSTRAINT fk_medicines_category 
        FOREIGN KEY (category_id) REFERENCES categories(id) 
        ON DELETE SET NULL ON UPDATE CASCADE
    ");

    // 4. Create an index on the new foreign key column for faster search speeds
    $pdo->exec("ALTER TABLE medicines ADD INDEX idx_category_id (category_id)");
};