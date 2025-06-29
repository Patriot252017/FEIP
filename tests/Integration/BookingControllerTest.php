<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Cottage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingControllerTest extends WebTestCase
{
    private $client;
    private $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = static::getContainer()->get('doctrine')->getManager();
        
        $this->em->getConnection()->executeStatement('DELETE FROM booking');
        $this->em->getConnection()->executeStatement('DELETE FROM cottage');
        $this->em->getConnection()->executeStatement('ALTER TABLE cottage AUTO_INCREMENT = 1');
        
        $cottage = new Cottage();
        $cottage->setBeds(2);
        $cottage->setDistanceFromSea(50);
        $this->em->persist($cottage);
        $this->em->flush();
    }

    public function testCreateBooking(): void
    {
        $this->client->request(
            'POST',
            '/api/bookings',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['phone' => '+123456789', 'cottageId' => 1])
        );

        $response = $this->client->getResponse();
        
        $this->assertEquals(201, $response->getStatusCode());
        
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        if ($this->em->getConnection()->isTransactionActive()) {
            $this->em->rollback();
        }
        
        $this->em->close();
        $this->em = null;
        $this->client = null;
    }
}