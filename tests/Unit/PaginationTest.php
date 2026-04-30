<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    public function testPaginateCalculation(): void
    {
        $result = paginate(100, 20, 1);

        $this->assertEquals(100, $result['total']);
        $this->assertEquals(20, $result['per_page']);
        $this->assertEquals(1, $result['current_page']);
        $this->assertEquals(5, $result['total_pages']);
        $this->assertEquals(0, $result['offset']);
        $this->assertTrue($result['has_more']);
    }

    public function testLastPage(): void
    {
        $result = paginate(100, 20, 5);
        $this->assertFalse($result['has_more']);
        $this->assertEquals(80, $result['offset']);
    }

    public function testSinglePage(): void
    {
        $result = paginate(5, 20, 1);
        $this->assertEquals(1, $result['total_pages']);
        $this->assertFalse($result['has_more']);
    }

    public function testOffsetCalculation(): void
    {
        $result = paginate(50, 10, 3);
        $this->assertEquals(20, $result['offset']);
    }
}
