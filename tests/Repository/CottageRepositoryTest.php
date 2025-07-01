<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Entity\Booking;
use App\Entity\Cottage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CottageRepositoryTest extends KernelTestCase
{
    private $entityManager;

    private $cottageRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->cottageRepository = $this->entityManager->getRepository(Cottage::class);

        $this->entityManager->getConnection()->executeStatement('DELETE FROM booking');
        $this->entityManager->getConnection()->executeStatement('DELETE FROM cottage');
    }

    public function testBasicPersistence(): void
    {
        $cottage = new Cottage();
        $cottage->setBeds(2);
        $cottage->setDistanceFromSea(100);

        $this->entityManager->persist($cottage);
        $this->entityManager->flush();

        $savedCottage = $this->cottageRepository->find($cottage->getId());
        $this->assertNotNull($savedCottage);
        $this->assertEquals(2, $savedCottage->getBeds());
    }

    public function testBookingsRelationship(): void
    {
        $cottage = new Cottage();
        $cottage->setBeds(3);
        $cottage->setDistanceFromSea(50);

        $booking1 = new Booking();
        $booking1->setPhone('+123456789');
        $booking1->setCottage($cottage);

        $booking2 = new Booking();
        $booking2->setPhone('+987654321');
        $booking2->setCottage($cottage);

        $this->entityManager->persist($cottage);
        $this->entityManager->persist($booking1);
        $this->entityManager->persist($booking2);
        $this->entityManager->flush();

        $this->entityManager->clear();
        $loadedCottage = $this->cottageRepository->find($cottage->getId());

        $bookings = $loadedCottage->getBookings();
        $this->assertCount(2, $bookings, 'Должно быть 2 бронирования');

        foreach ($bookings as $booking) {
            $this->assertEquals($loadedCottage->getId(), $booking->getCottage()->getId());
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
