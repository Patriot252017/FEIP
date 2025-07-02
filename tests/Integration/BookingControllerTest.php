<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use App\Entity\Cottage;
use Doctrine\ORM\EntityManagerInterface;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser as KernelBrowser;

final class BookingControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->client = static::createClient();
        $container = static::getContainer();
        
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $this->entityManager = $entityManager;
        
        // Очистка базы данных перед тестом
        $this->entityManager->getConnection()->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        $this->entityManager->getConnection()->executeStatement('TRUNCATE cottage');
        $this->entityManager->getConnection()->executeStatement('TRUNCATE booking');
        $this->entityManager->getConnection()->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
        
        // Создание тестовых данных
        $cottage = new Cottage();
        $cottage->setBeds(2);
        $cottage->setDistanceFromSea(100);
        $this->entityManager->persist($cottage);
        $this->entityManager->flush();
    }

    public function testCreateBooking(): void
    {
        $jsonData = json_encode([
            'phone' => '+123456789', 
            'cottageId' => 1
        ]);
        $this->assertNotFalse($jsonData);
        
        $this->client->request(
            'POST',
            '/api/bookings',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $response = $this->client->getResponse();
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJson($response->getContent());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('success', $responseData['status']);
    }

    #[Override]
    protected function tearDown(): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }
        $this->entityManager->close();
        parent::tearDown();
    }
}
