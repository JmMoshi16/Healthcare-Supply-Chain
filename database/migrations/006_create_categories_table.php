<?php

return function (PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $pdo->exec("
        INSERT IGNORE INTO categories (name)
        SELECT DISTINCT category FROM medicines WHERE category IS NOT NULL AND category != ''
    ");

    // Add category_id only if it doesn't exist
    $cols = array_column($pdo->query("SHOW COLUMNS FROM medicines")->fetchAll(), 'Field');
    if (!in_array('category_id', $cols)) {
        $pdo->exec("ALTER TABLE medicines ADD COLUMN category_id INT NULL AFTER category");
    }

    $pdo->exec("
        UPDATE medicines m
        JOIN categories c ON c.name = m.category
        SET m.category_id = c.id
    ");

    // Add FK only if it doesn't exist
    $constraints = array_column(
        $pdo->query("
            SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'medicines'
            AND CONSTRAINT_NAME = 'fk_medicines_category'
        ")->fetchAll(),
        'CONSTRAINT_NAME'
    );

    if (empty($constraints)) {
        $pdo->exec("
            ALTER TABLE medicines
            ADD CONSTRAINT fk_medicines_category
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
        ");
    }
};
