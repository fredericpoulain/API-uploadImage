<?php

namespace Integration;


use App\DataFixtures\AdminFixtures;
use App\Entity\User;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminFixturesTest extends WebTestCase
{
    // Property for DatabaseTool service
    /** @var AbstractDatabaseTool */
    protected mixed $databaseTool;

    /**
     * @return void
     */
    public function setUp(): void
    {
        // The parent method is called
        parent::setUp();
        // We retrieve the DatabaseTool service from the container
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    /**
     * @return void
     */
    public function testAdminFixtures(): void
    {
        // Load the AdminFixtures
        $this->databaseTool->loadFixtures([AdminFixtures::class]);
        // Get the entity manager
        $em = static::getContainer()->get('doctrine')->getManager();
        // Get all registered users using AdminFixtures
        $users = $em->getRepository(User::class)->findAll();
        // And check if the admin registration has been done
        $this->assertCount(1, $users);
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        // The parent method is called
        parent::tearDown();
        // And we empty the DatabaseTool property
        unset($this->databaseTool);
    }
}
