<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\ApiToken;

class ApiTest extends TestCase
{
    public function testApiTokenTableName(): void
    {
        $reflection = new \ReflectionClass(ApiToken::class);
        $prop       = $reflection->getProperty('table');
        $prop->setAccessible(true);
        $this->assertEquals('api_tokens', $prop->getValue(new ApiToken()));
    }

    public function testApiTokenFillable(): void
    {
        $reflection = new \ReflectionClass(ApiToken::class);
        $prop       = $reflection->getProperty('fillable');
        $prop->setAccessible(true);
        $fillable = $prop->getValue(new ApiToken());

        $this->assertContains('user_id', $fillable);
        $this->assertContains('token', $fillable);
        $this->assertContains('expires_at', $fillable);
    }

    public function testGeneratedTokenIsBase64(): void
    {
        $token = generate_api_token();
        $decoded = base64_decode($token, true);
        $this->assertNotFalse($decoded);
        $this->assertEquals(64, strlen($decoded));
    }
}
