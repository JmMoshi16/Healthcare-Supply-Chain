<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Stock;

class StockTest extends TestCase
{
    public function testStockTableName(): void
    {
        $reflection = new \ReflectionClass(Stock::class);
        $prop       = $reflection->getProperty('table');
        $prop->setAccessible(true);
        $this->assertEquals('stocks', $prop->getValue(new Stock()));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Stock::class);
        $prop       = $reflection->getProperty('fillable');
        $prop->setAccessible(true);
        $fillable = $prop->getValue(new Stock());

        $this->assertContains('batch_id', $fillable);
        $this->assertContains('transaction_type', $fillable);
        $this->assertContains('quantity', $fillable);
        $this->assertContains('performed_by', $fillable);
    }
}
