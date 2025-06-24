<?php

namespace App\Tests\Unit\Service;

use App\Service\HomeDataService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class HomeDataServiceTest extends TestCase
{
    private string $testFile;
    private HomeDataService $service;

    protected function setUp(): void
    {
        $this->testFile = sys_get_temp_dir().'/test_cottages.csv';
        (new Filesystem())->dumpFile($this->testFile, "id,amenities,beds,distanceFromSea\n1,санузел,2,100\n2,душевая кабина,4,50");
        
        $this->service = new HomeDataService($this->testFile);
    }

    protected function tearDown(): void
    {
        @unlink($this->testFile);
    }

    public function testGetAvailableCottages(): void
    {
        $cottages = $this->service->getAvailableCottages();
        
        $this->assertCount(2, $cottages);
        $this->assertEquals('санузел', $cottages[0]['amenities']);
        $this->assertEquals(4, $cottages[1]['beds']);
    }

    public function testCottageExists(): void
    {
        $this->assertTrue($this->service->cottageExists(1));
        $this->assertTrue($this->service->cottageExists(2));
        $this->assertFalse($this->service->cottageExists(999));
    }

    public function testGetCottage(): void
    {
        $cottage = $this->service->getCottage(1);
        $this->assertEquals(100, $cottage['distanceFromSea']);
        
        $this->assertNull($this->service->getCottage(999));
    }

    public function testFileNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        new HomeDataService('/nonexistent/file.csv');
    }
}