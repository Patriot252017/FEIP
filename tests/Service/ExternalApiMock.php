<?php

namespace App\Tests\Service;

use App\Service\ExternalApiInterface;

class ExternalApiMock implements ExternalApiInterface
{
    private array $mockResponses = [];

    /**
     * Задать мок-ответ для метода API
     */
    public function mockResponse(string $method, $response): void
    {
        $this->mockResponses[$method] = $response;
    }

    public function getCottages(): array
    {
        if (isset($this->mockResponses['getCottages']) && 
            is_callable($this->mockResponses['getCottages'])) {
            return call_user_func($this->mockResponses['getCottages']);
        }
    
        return $this->mockResponses['getCottages'] ?? [
            [
                'id' => 1, 
                'name' => 'Тестовый домик 1',
                'amenities' => 'санузел',
                'beds' => 2,
                'distanceFromSea' => 100
            ],
            [
                'id' => 2, 
                'name' => 'Тестовый домик 2',
                'amenities' => 'душевая кабина',
                'beds' => 4,
                'distanceFromSea' => 50
            ]
        ];
    }

    public function createBooking(array $data): bool
    {
        return $this->mockResponses['createBooking'] ?? true;
    }
}