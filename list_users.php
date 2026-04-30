<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=healthcare_supply;charset=utf8', 'root', '');
    $rows = $pdo->query('SELECT id, fullname, email, role, created_at FROM users ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rows)) {
        echo "No users found in the database.\n";
    } else {
        printf("%-4s %-25s %-35s %-12s %s\n", 'ID', 'Full Name', 'Email', 'Role', 'Created');
        echo str_repeat('-', 95) . "\n";
        foreach ($rows as $r) {
            printf("%-4s %-25s %-35s %-12s %s\n", $r['id'], $r['fullname'], $r['email'], $r['role'], $r['created_at']);
        }
        echo "\nTotal: " . count($rows) . " account(s)\n";
    }
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
