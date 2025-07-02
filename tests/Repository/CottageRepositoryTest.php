<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Entity\Booking;
use App\Entity\Cottage;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CottageRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private \Doctrine\Persistence\ObjectRepository $cottageRepository;

    #[Override]
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

        $booking1 = new Booking($cottage, '+123456789');
        $booking2 = new Booking($cottage, '+987654321');

        $this->entityManager->persist($cottage);
        $this->entityManager->persist($booking1);
        $this->entityManager->persist($booking2);
        $this->entityManager->flush();

        $this->entityManager->clear();
        $loadedCottage = $this->cottageRepository->find($cottage->getId());

        /** @var Collection<int, Booking> $bookings */
        $bookings = $loadedCottage->getBookings();
        $bookingsArray = $bookings->toArray();
        $this->assertCount(2, $bookingsArray);

        foreach ($bookingsArray as $booking) {
            $this->assertEquals($loadedCottage->getId(), $booking->getCottage()->getId());
        }
    }

    #[Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
