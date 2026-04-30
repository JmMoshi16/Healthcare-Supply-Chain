<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Batch;

class BatchTest extends TestCase
{
    public function testBatchTableName(): void
    {
        $reflection = new \ReflectionClass(Batch::class);
        $prop       = $reflection->getProperty('table');
        $prop->setAccessible(true);
        $this->assertEquals('batches', $prop->getValue(new Batch()));
    }

    public function testFillableContainsExpiryDate(): void
    {
        $reflection = new \ReflectionClass(Batch::class);
        $prop       = $reflection->getProperty('fillable');
        $prop->setAccessible(true);
        $fillable = $prop->getValue(new Batch());

        $this->assertContains('expiry_date', $fillable);
        $this->assertContains('batch_number', $fillable);
        $this->assertContains('medicine_id', $fillable);
    }

    public function testInsufficientStockThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient stock');

        $batch = $this->getMockBuilder(Batch::class)
            ->onlyMethods(['find', 'update'])
            ->getMock();

        $batch->method('find')->willReturn([
            'id'               => 1,
            'current_quantity' => 5,
        ]);

        $batch->updateStock(1, 10, 'out');
    }
}
