<?php

namespace App\Service;

use App\Entity\Cottage;

class HomeDataService
{
    private string $cottagesFile;

    public function __construct(string $cottagesFile)
    {
        $this->cottagesFile = $cottagesFile;
    }

    public function getAvailableCottages(): array
    {
        if (!file_exists($this->cottagesFile)) {
            throw new \RuntimeException('Cottages file not found: '.$this->cottagesFile);
        }

        $data = array_map('str_getcsv', file($this->cottagesFile));
        $headers = array_shift($data);
        $cottages = [];

        foreach ($data as $item) {
            if (count($item) !== count($headers)) {
                continue;
            }
        
            $cottageData = array_combine($headers, $item);
            $cottage = new Cottage();
            $cottage->setId((int)$cottageData['id']);
            $cottage->setAmenities($cottageData['amenities']);
            $cottage->setBeds((int)$cottageData['beds']);
            $cottage->setDistanceFromSea((int)$cottageData['distanceFromSea']);
        
            $cottages[] = [
                'id' => $cottage->getId(),
                'amenities' => $cottage->getAmenities(),
                'beds' => $cottage->getBeds(),
                'distanceFromSea' => $cottage->getDistanceFromSea()
            ];
        }

        return $cottages;
    }
}