<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cottage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cottage>
 */
class CottageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cottage::class);
    }

    public function findByDistanceFromSea(int $distance): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.distanceFromSea = :distance')
            ->setParameter('distance', $distance)
            ->getQuery()
            ->getResult();
    }

    public function findAvailable(): array
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ->getResult();
    }
}
