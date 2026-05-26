<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Medicine;

class MedicineTest extends TestCase
{
    public function testMedicineHasCorrectTable(): void
    {
        $medicine = new Medicine();
        $this->assertInstanceOf(Medicine::class, $medicine);
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Medicine::class);
        $prop       = $reflection->getProperty('fillable');
        $prop->setAccessible(true);
        $fillable = $prop->getValue(new Medicine());

        $this->assertContains('name', $fillable);
        $this->assertContains('category_id', $fillable);
        $this->assertContains('unit', $fillable);
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Medicine::class);
        $prop       = $reflection->getProperty('table');
        $prop->setAccessible(true);
        $this->assertEquals('medicines', $prop->getValue(new Medicine()));
    }
}
