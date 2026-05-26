<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Category;

class CategoryTest extends TestCase
{
    public function testCategoryTableName(): void
    {
        $reflection = new \ReflectionClass(Category::class);
        $prop       = $reflection->getProperty('table');
        $prop->setAccessible(true);
        $this->assertEquals('categories', $prop->getValue(new Category()));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Category::class);
        $prop       = $reflection->getProperty('fillable');
        $prop->setAccessible(true);
        $fillable = $prop->getValue(new Category());

        $this->assertContains('name', $fillable);
    }
}
