<?php

namespace App\Service;

use App\Entity\Cottage;

class HomeDataService
{
    private string $cottagesFile;
    private array $cottagesCache = [];

    public function __construct(string $cottagesFile)
    {
        $this->cottagesFile = $cottagesFile;
        $this->loadCottages();
    }

    private function loadCottages(): void
    {
        if (!file_exists($this->cottagesFile)) {
            throw new \RuntimeException('Cottages file not found: '.$this->cottagesFile);
        }

        $data = array_map(function($line) {
            return str_getcsv($line, ',', '"', '\\');
        }, file($this->cottagesFile));
        
        $headers = array_shift($data);
        
        foreach ($data as $item) {
            if (count($item) !== count($headers)) {
                continue;
            }
            
            $cottageData = array_combine($headers, $item);
            $this->cottagesCache[(int)$cottageData['id']] = [
                'id' => (int)$cottageData['id'],
                'amenities' => $cottageData['amenities'],
                'beds' => (int)$cottageData['beds'],
                'distanceFromSea' => (int)$cottageData['distanceFromSea']
            ];
        }
    }

    public function getAvailableCottages(): array
    {
        return array_values($this->cottagesCache);
    }

    public function cottageExists(int $cottageId): bool
    {
        return isset($this->cottagesCache[$cottageId]);
    }

    public function getCottage(int $cottageId): ?array
    {
        return $this->cottagesCache[$cottageId] ?? null;
    }
}