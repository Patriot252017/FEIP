<?php

namespace App\Tests\Unit\Service;

use App\Service\BookingService;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem;

class BookingServiceTest extends TestCase
{
    private string $testFile;
    private BookingService $service;

    protected function setUp(): void
    {
        $this->testFile = sys_get_temp_dir().'/test_bookings.csv';
        (new Filesystem())->dumpFile($this->testFile, "id,phone,cottageId,comment,createdAt\n");
        
        $this->service = new BookingService($this->testFile, new NullLogger());
    }

    protected function tearDown(): void
    {
        @unlink($this->testFile);
    }

    public function testCreateBooking(): void
    {
        $result = $this->service->createBooking('+79991234567', 1, 'Test');
        $this->assertTrue($result);
        
        $bookings = $this->readCsv();
        $this->assertCount(1, $bookings);
        $this->assertEquals('+79991234567', $bookings[0]['phone']);
    }

    public function testInvalidPhone(): void
    {
        $result = $this->service->createBooking('invalid', 1, 'Test');
        $this->assertFalse($result);
    }

    public function testUpdateBooking(): void
    {
        $this->service->createBooking('+79991234567', 1, 'Old comment');
        $id = $this->readCsv()[0]['id'];
        
        $result = $this->service->updateBooking($id, 'New comment');
        $this->assertTrue($result);
        
        $updated = $this->service->getBooking($id);
        $this->assertEquals('New comment', $updated['comment']);
    }

    private function readCsv(): array
    {
        $rows = array_map('str_getcsv', file($this->testFile));
        $headers = array_shift($rows);
        return array_map(fn($row) => array_combine($headers, $row), $rows);
    }
}