<?php

namespace Functionnal;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminLoginTest extends WebTestCase
{
    /**
     * @return void
     */
    public function testSubmitValidData(): void
    {

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        // Create a test admin with data
        $testAdmin = $userRepository->findOneByEmail('admin@dashboardUpload.com');

        // We use the loginUser() method to simulate the login
        $client->loginUser($testAdmin);

        $client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Dashboard');
    }
}