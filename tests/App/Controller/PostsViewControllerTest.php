<?php


namespace App\Tests\App\Controller;


use App\DataFixtures\ORM\LoadCommentsFixtures;
use App\DataFixtures\ORM\LoadUserFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostsViewControllerTest extends WebTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    private $testClient = null;

    public function setUp(): void
    {
        $this->testClient = static::createClient();
        $container = self::$kernel->getContainer();
        $this->databaseTool = $container->get(DatabaseToolCollection::class)->get( null,'doctrine');
        $this->databaseTool->loadFixtures(
            [LoadCommentsFixtures::class]
        );
    }

    public function testItDisplaysPosts()
    {
        $crawler = $this->testClient->request('GET', '/pofsdfsts',[],[],['ACCEPT'=>'Application/json']);
        dd($this->testClient->getResponse()->getContent());


    }
}