<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cottage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cottage>
 */
final class CottageRepository extends ServiceEntityRepository
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @psalm-suppress UnusedParam
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cottage::class);
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function findByDistanceFromSea(int $distance): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.distanceFromSea = :distance')
            ->setParameter('distance', $distance)
            ->getQuery()
            ->getResult();
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function findAvailable(): array
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ->getResult();
    }
}
