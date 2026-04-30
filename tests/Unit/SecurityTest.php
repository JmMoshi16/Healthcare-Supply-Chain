<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
    public function testPasswordHashing(): void
    {
        $hash = hash_password('Admin@123');
        $this->assertTrue(password_verify('Admin@123', $hash));
    }

    public function testPasswordHashIsUnique(): void
    {
        $hash1 = hash_password('Admin@123');
        $hash2 = hash_password('Admin@123');
        $this->assertNotEquals($hash1, $hash2);
    }

    public function testVerifyPasswordFails(): void
    {
        $hash = hash_password('correct');
        $this->assertFalse(verify_password('wrong', $hash));
    }

    public function testEscapeOutput(): void
    {
        $input    = '<script>alert("xss")</script>';
        $escaped  = esc($input);
        $this->assertStringNotContainsString('<script>', $escaped);
        $this->assertStringContainsString('&lt;script&gt;', $escaped);
    }

    public function testCsrfTokenGeneration(): void
    {
        // csrf_token() requires session; test token format via generate_token
        $token = generate_token(32);
        $this->assertEquals(64, strlen($token)); // 32 bytes = 64 hex chars
    }

    public function testApiTokenGeneration(): void
    {
        $token = generate_api_token();
        $this->assertNotEmpty($token);
        $this->assertIsString($token);
    }
}
