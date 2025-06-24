<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Factory\UserFactory;  

class AuthenticationApiTest extends WebTestCase
{
    public function testApiAuthentication()
    {
        $client = static::createClient();
        
        // Сначала создайте тестового пользователя
        UserFactory::createOne([
            'email' => 'user@example.com',
            'password' => 'password'
        ]);
        
        $client->request('POST', '/api/login', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'user@example.com',
                'password' => 'password'
            ])
        );
        
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
    }
}