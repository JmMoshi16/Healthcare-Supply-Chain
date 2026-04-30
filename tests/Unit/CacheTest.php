<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Core\Cache;

class CacheTest extends TestCase
{
    private string $key = 'test_cache_key';

    protected function tearDown(): void
    {
        Cache::forget($this->key);
    }

    public function testSetAndGet(): void
    {
        Cache::set($this->key, 'hello', 60);
        $this->assertEquals('hello', Cache::get($this->key));
    }

    public function testMissReturnsNull(): void
    {
        $this->assertNull(Cache::get('nonexistent_key_xyz'));
    }

    public function testForget(): void
    {
        Cache::set($this->key, 'value', 60);
        Cache::forget($this->key);
        $this->assertNull(Cache::get($this->key));
    }

    public function testHas(): void
    {
        Cache::set($this->key, 'data', 60);
        $this->assertTrue(Cache::has($this->key));
        Cache::forget($this->key);
        $this->assertFalse(Cache::has($this->key));
    }
}
