<?php
namespace App\Tests\App\Controller;
use App\DataFixtures\ORM\LoadUserFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    private $testClient = null;

    public function setUp(): void
    {
        $this->testClient = static::createClient();
        $container = self::$kernel->getContainer();
        $this->databaseTool = $this->testClient->getContainer()->get(DatabaseToolCollection::class)->get( null,'doctrine');
    }

    public function testLogin()
    {
        $this->databaseTool->loadFixtures(
            [LoadUserFixtures::class]
        );

        $crawler = $this->testClient->request('POST', '/login',[],[],[],"{\"username\": \"admin\",\"password\": \"1234\"}");

        $this->assertResponseStatusCodeSame(200);

        $crawler = $this->testClient->request('POST', '/login',[],[],[],"{\"username\": \"admin\",\"password\": \"12344\"}");
        $this->assertResponseStatusCodeSame(401);
    }
}