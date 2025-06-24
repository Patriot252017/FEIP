<?php

namespace App\Tests\Integration;

use App\Service\ExternalApiInterface;
use App\Tests\Service\ExternalApiMock;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

class BookingControllerWithMockTest extends WebTestCase
{
    private static ExternalApiMock $apiMock;

    public static function setUpBeforeClass(): void
    {
        self::$apiMock = new ExternalApiMock();
    }

    public function testExternalApiGetCottages(): void
    {
        $client = static::createClient();
        
        // Настраиваем мок
        self::$apiMock->mockResponse('getCottages', [
            [
                'id' => 99,
                'name' => 'Тестовый домик',
                'amenities' => 'санузел',
                'beds' => 2,
                'distanceFromSea' => 100
            ]
        ]);
        
        // Подменяем сервис в контейнере
        $client->getContainer()->set(ExternalApiInterface::class, self::$apiMock);
        
        $client->request('GET', '/api/cottages/external');
        
        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertEquals('Тестовый домик', $response[0]['name']);
        $this->assertEquals(2, $response[0]['beds']);
    }

    public function testExternalApiCreateBooking(): void
    {
        $client = static::createClient();
        
        // Настраиваем мок
        self::$apiMock->mockResponse('createBooking', true);
        
        $client->getContainer()->set(ExternalApiInterface::class, self::$apiMock);
        
        $client->request(
            'POST',
            '/api/bookings/external',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'phone' => '+79991234567',
                'cottageId' => 99,
                'comment' => 'Тестовое бронирование'
            ])
        );
        
        $this->assertResponseStatusCodeSame(201);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('success', $response['status']);
    }

    public function testExternalApiErrorHandling(): void
    {
        $client = static::createClient();
        
        // Настраиваем мок для ошибки
        self::$apiMock->mockResponse('getCottages', function() {
            throw new \RuntimeException('API недоступно');
        });
        
        $client->getContainer()->set(ExternalApiInterface::class, self::$apiMock);
        
        $client->request('GET', '/api/cottages/external');
        
        $this->assertResponseStatusCodeSame(500);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('error', $response['status']);
    }
}