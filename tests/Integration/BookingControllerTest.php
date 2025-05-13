public function testCreateBooking(): void
{
    $client = static::createClient();
    $client->request(
        'POST',
        '/api/bookings',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode(['phone' => '+123456789', 'cottageId' => 1])
    );

    $this->assertEquals(201, $client->getResponse()->getStatusCode());
    $response = json_decode($client->getResponse()->getContent(), true);
    $this->assertEquals('success', $response['status']);
}