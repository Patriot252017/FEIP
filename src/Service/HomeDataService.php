<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\CottageRepository;

class HomeDataService
{
    public function __construct(
        private CottageRepository $cottageRepository,
    ) {
    }

    public function getAvailableCottages(): array
    {
        $cottages = $this->cottageRepository->findAll();

        return array_map(function ($cottage) {
            return [
                'id' => $cottage->getId(),
                'amenities' => $cottage->getAmenities(),
                'beds' => $cottage->getBeds(),
                'distanceFromSea' => $cottage->getDistanceFromSea(),
            ];
        }, $cottages);
    }
}
