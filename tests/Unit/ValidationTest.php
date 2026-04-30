<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Core\Validator;

class ValidationTest extends TestCase
{
    public function testRequiredRule(): void
    {
        $v = new Validator(['name' => '']);
        $this->assertFalse($v->validate(['name' => 'required']));
        $this->assertArrayHasKey('name', $v->errors());
    }

    public function testEmailRule(): void
    {
        $v = new Validator(['email' => 'not-an-email']);
        $this->assertFalse($v->validate(['email' => 'required|email']));
    }

    public function testMinRule(): void
    {
        $v = new Validator(['name' => 'ab']);
        $this->assertFalse($v->validate(['name' => 'required|min:3']));
    }

    public function testMaxRule(): void
    {
        $v = new Validator(['name' => str_repeat('a', 256)]);
        $this->assertFalse($v->validate(['name' => 'required|max:255']));
    }

    public function testPassesValidation(): void
    {
        $v = new Validator(['name' => 'Paracetamol', 'email' => 'test@test.com']);
        $this->assertTrue($v->validate(['name' => 'required|min:3', 'email' => 'required|email']));
        $this->assertEmpty($v->errors());
    }
}
