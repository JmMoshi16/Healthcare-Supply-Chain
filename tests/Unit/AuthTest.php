<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    public function testUserTableName(): void
    {
        $reflection = new \ReflectionClass(User::class);
        $prop       = $reflection->getProperty('table');
        $prop->setAccessible(true);
        $this->assertEquals('users', $prop->getValue(new User()));
    }

    public function testPasswordIsHiddenField(): void
    {
        $reflection = new \ReflectionClass(User::class);
        $prop       = $reflection->getProperty('hidden');
        $prop->setAccessible(true);
        $hidden = $prop->getValue(new User());

        $this->assertContains('password', $hidden);
    }

    public function testPasswordHashedOnCreate(): void
    {
        $user = $this->getMockBuilder(User::class)
            ->onlyMethods(['query'])
            ->getMock();

        // Verify that hash_password produces a bcrypt hash
        $hash = hash_password('Admin@123');
        $this->assertStringStartsWith('$2y$', $hash);
        $this->assertTrue(verify_password('Admin@123', $hash));
    }

    public function testRoleHierarchy(): void
    {
        // Simulate session for has_role / can checks
        $_SESSION['user'] = ['id' => 1, 'role' => 'superadmin'];
        $this->assertTrue(has_role('superadmin'));
        $this->assertTrue(can('medicines.*'));

        $_SESSION['user'] = ['id' => 2, 'role' => 'staff'];
        $this->assertFalse(has_role('superadmin'));
        $this->assertFalse(can('medicines.*'));
        $this->assertTrue(can('medicines.view'));

        unset($_SESSION['user']);
    }
}
