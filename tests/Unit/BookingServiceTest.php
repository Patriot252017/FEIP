<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Booking;
use App\Entity\Cottage;
use App\Repository\BookingRepository;
use App\Repository\CottageRepository;
use App\Service\BookingService;
use Doctrine\ORM\EntityManagerInterface;
use Override;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class BookingServiceTest extends KernelTestCase
{
    private BookingService $service;
    private CottageRepository $cottageRepo;
    private BookingRepository $bookingRepo;
    private EntityManagerInterface $em;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        
        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        
        $this->clearDatabase();
        
        $this->cottageRepo = $this->em->getRepository(Cottage::class);
        $this->bookingRepo = $this->em->getRepository(Booking::class);
        
        // Используем NullLogger вместо реального логгера
        $logger = new NullLogger();
        
        $this->service = new BookingService(
            $this->bookingRepo,
            $this->cottageRepo,
            $this->em,
            $logger
        );
    }

    private function clearDatabase(): void
    {
        $connection = $this->em->getConnection();
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeStatement('TRUNCATE TABLE booking');
        $connection->executeStatement('TRUNCATE TABLE cottage');
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function testCreateBookingSuccess(): void
    {
        // Создаем и сохраняем коттедж
        $cottage = new Cottage();
        $cottage->setBeds(2);
        $cottage->setDistanceFromSea(100);
        $this->em->persist($cottage);
        $this->em->flush();
        
        // Явно проверяем что ID установлен
        $cottageId = $cottage->getId();
        $this->assertNotNull($cottageId, 'Cottage ID should not be null after persist');
        
        // Теперь передаем ID, который гарантированно не null
        $result = $this->service->createBooking('+123456789', $cottageId, 'Test');
        $this->assertTrue($result);
    }

    #[Override]
    protected function tearDown(): void
    {
        $this->em->close();
        parent::tearDown();
    }
}
