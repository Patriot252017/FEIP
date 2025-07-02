<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\CottageRepository;

final class HomeDataService
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private CottageRepository $cottageRepository,
    ) {
    }

    public function getAvailableCottages(): array
    {
        $cottages = $this->cottageRepository->findAll();

        return array_map(fn ($cottage) => [
            'id' => $cottage->getId(),
            'amenities' => $cottage->getAmenities(),
            'beds' => $cottage->getBeds(),
            'distanceFromSea' => $cottage->getDistanceFromSea(),
        ], $cottages);
    }
}
