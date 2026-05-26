<?php

/**
 * UserSeeder — seeds default accounts for HealthChain.
 *
 * Default credentials:
 *   SuperAdmin : admin@healthcare.com   / Admin@123
 *   Manager    : manager@healthcare.com / Manager@123
 *   Staff      : staff@healthcare.com   / Staff@123
 */
return function (PDO $pdo): void {
    $users = [
        [
            'fullname'  => 'System Administrator',
            'email'     => 'admin@healthcare.com',
            'password'  => 'Admin@123',
            'role'      => 'superadmin',
            'is_active' => 1,
        ],
        [
            'fullname'  => 'Inventory Manager',
            'email'     => 'manager@healthcare.com',
            'password'  => 'Manager@123',
            'role'      => 'manager',
            'is_active' => 1,
        ],
        [
            'fullname'  => 'Pharmacy Staff',
            'email'     => 'staff@healthcare.com',
            'password'  => 'Staff@123',
            'role'      => 'staff',
            'is_active' => 1,
        ],
    ];

    $stmt = $pdo->prepare("
        INSERT IGNORE INTO users (fullname, email, password, role, is_active)
        VALUES (:fullname, :email, :password, :role, :is_active)
    ");

    foreach ($users as $user) {
        $stmt->execute([
            ':fullname'  => $user['fullname'],
            ':email'     => $user['email'],
            ':password'  => password_hash($user['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            ':role'      => $user['role'],
            ':is_active' => $user['is_active'],
        ]);
    }

    echo "  ✓ Seeded " . count($users) . " default users.\n";
};
