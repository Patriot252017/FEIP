<?php

namespace App\Tests\Repository;

use App\Entity\Cottage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CottageRepositoryTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testSearchByDistance()
    {
        $cottage = new Cottage();
        $cottage->setBeds(2);
        $cottage->setDistanceFromSea(50);
        $this->entityManager->persist($cottage);
        $this->entityManager->flush();

        $cottages = $this->entityManager
            ->getRepository(Cottage::class)
            ->findByDistanceFromSea(50);

        $this->assertCount(1, $cottages);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Очистка БД после каждого теста
        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('DELETE FROM cottage');
        $connection->executeStatement('ALTER TABLE cottage AUTO_INCREMENT = 1');
        $this->entityManager->close();
        $this->entityManager = null;
    }
}