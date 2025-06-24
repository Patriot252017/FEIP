<?php

namespace App\Tests\Security;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Factory\UserFactory;

class AuthenticationTest extends WebTestCase
{
    public function testLoginPageLoadsSuccessfully(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

    public function testSuccessfulLogin(): void
    {
        $client = static::createClient();
        
        // 1. Получаем контейнер сервисов
        $container = static::getContainer();
        
        // 2. Получаем UserRepository
        $userRepository = $container->get('doctrine')->getRepository(User::class);
        
        // 3. Находим тестового пользователя (должен существовать в БД)
        $testUser = $userRepository->findOneByEmail('user@example.com');
        
        // 4. Логиним пользователя
        $client->loginUser($testUser);
        
        // 5. Проверяем доступ к защищенной странице
        $client->request('GET', '/profile');
        $this->assertResponseIsSuccessful();
    }

    public function testAdminAccess(): void
    {
        $client = static::createClient();
        
        // Находим пользователя с ролью ADMIN
        $adminUser = static::getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@example.com']);
        
        $client->loginUser($adminUser);
        
        $client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();
    }

    public function testLogin()
    {
        $user = UserFactory::createOne([
            'email' => 'test@example.com',
            'password' => 'password'
        ]);
        
        $client = static::createClient();
        $client->loginUser($user->_real());
    }
    
}