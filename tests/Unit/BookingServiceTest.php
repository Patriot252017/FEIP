<?php

namespace App\Tests\Unit\Service;

use App\Entity\Booking;
use App\Entity\Cottage;
use App\Repository\BookingRepository;
use App\Repository\CottageRepository;
use App\Service\BookingService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class BookingServiceTest extends TestCase
{
    private BookingService $service;
    private $cottageRepoMock;
    private $bookingRepoMock;
    private $emMock;
    private $loggerMock;

    protected function setUp(): void
    {
        $this->cottageRepoMock = $this->createMock(CottageRepository::class);
        $this->bookingRepoMock = $this->createMock(BookingRepository::class);
        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->service = new BookingService(
            $this->bookingRepoMock,
            $this->cottageRepoMock,
            $this->emMock,
            $this->loggerMock
        );
    }

    public function testCreateBookingSuccess(): void
    {
        $cottage = new Cottage();
        $this->cottageRepoMock->method('find')->willReturn($cottage);
        
        $this->emMock->expects($this->once())->method('persist');
        $this->emMock->expects($this->once())->method('flush');

        $result = $this->service->createBooking('+123456789', 1, 'Test');
        $this->assertTrue($result);
    }
}