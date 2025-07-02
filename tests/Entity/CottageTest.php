<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Cottage;
use Doctrine\ORM\EntityManagerInterface;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CottageTest extends KernelTestCase
{
    private EntityManagerInterface $em;

    #[Override]
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testCottagePersistence(): void
    {
        $cottage = new Cottage();
        $cottage->setBeds(2);
        $cottage->setDistanceFromSea(50);
        $cottage->setAmenities('toilet');

        $this->em->persist($cottage);
        $this->em->flush();

        $this->assertNotNull($cottage->getId());
    }

    #[Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
    }
}
