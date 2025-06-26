<?php

namespace App\Tests\integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Psr\Log\NullLogger;
use App\Service\HomeDataService;
use App\Service\BookingService;

class BookingControllerTest extends WebTestCase
{
    private static string $testBookingsFile;
    private static string $testCottagesFile;
    private static NullLogger $logger;

    public static function setUpBeforeClass(): void
    {
        self::$testBookingsFile = __DIR__.'/../fixtures/test_bookings.csv';
        self::$testCottagesFile = __DIR__.'/../fixtures/test_cottages.csv';
        self::$logger = new NullLogger();

        $filesystem = new Filesystem();
        $filesystem->dumpFile(self::$testCottagesFile, "id,amenities,beds,distanceFromSea\n1,санузел,2,100\n2,душевая кабина,4,50\n3,никаких,6,200");
        $filesystem->dumpFile(self::$testBookingsFile, "id,phone,cottageId,comment,createdAt\n");
    }

    protected function tearDown(): void
    {
        (new Filesystem())->dumpFile(self::$testBookingsFile, "id,phone,cottageId,comment,createdAt\n");
        parent::tearDown();
        static::ensureKernelShutdown();
    }

    public function testCreateBooking(): void
    {
        $client = static::createClient();
        
        $container = $client->getContainer();
        $container->set(HomeDataService::class, 
            new HomeDataService(self::$testCottagesFile));
        $container->set(BookingService::class, 
            new BookingService(self::$testBookingsFile, self::$logger));

        $client->request(
            'POST',
            '/api/bookings',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'phone' => '+1234567890',
                'cottageId' => 1,
                'comment' => 'Test booking'
            ])
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            '{"status":"success"}',
            $client->getResponse()->getContent()
        );

        $bookings = $this->getBookingsFromCsv();
        $this->assertCount(1, $bookings);
    }

    private function getBookingsFromCsv(): array
    {
        $rows = array_map(function($line) {
            return str_getcsv($line, ',', '"', '\\');
        }, file(self::$testBookingsFile));
        
        $headers = array_shift($rows);
        $bookings = [];
        
        foreach ($rows as $row) {
            if (count($row) === count($headers)) {
                $bookings[] = array_combine($headers, $row);
            }
        }
        
        return $bookings;
    }

    public function testCreateBookingWithInvalidCottage(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $container->set(HomeDataService::class, 
            new HomeDataService(self::$testCottagesFile));
        $container->set(BookingService::class, 
            new BookingService(self::$testBookingsFile, self::$logger));

        $client->request(
            'POST',
            '/api/bookings',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'phone' => '+1234567890',
                'cottageId' => 999,
                'comment' => 'Invalid cottage'
            ])
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testCreateBookingWithInvalidPhone(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $container->set(HomeDataService::class, 
            new HomeDataService(self::$testCottagesFile));
        $container->set(BookingService::class, 
            new BookingService(self::$testBookingsFile, self::$logger));

        $client->request(
            'POST',
            '/api/bookings',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'phone' => 'invalid',
                'cottageId' => 1,
                'comment' => 'Invalid phone'
            ])
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
}