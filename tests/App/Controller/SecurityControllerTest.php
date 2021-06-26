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
        $this->databaseTool = $container->get(DatabaseToolCollection::class)->get( null,'doctrine');
        $this->databaseTool->loadFixtures(
            [LoadUserFixtures::class]
        );
    }

    public function testLogin()
    {
        $crawler = $this->testClient->request('POST', '/login',[],[],[],"{\"username\": \"admin\",\"password\": \"1234\"}");

        $this->assertResponseStatusCodeSame(200);
        $crawler = $this->testClient->request('POST', '/login',[],[],[],"{\"username\": \"admin\",\"password\": \"12344\"}");
        $this->assertResponseStatusCodeSame(401);
    }
    public function testItRefuseNameDuplication()
    {
        $crawler = $this->testClient->request('POST', '/register',[],[],[],
            "{\"username\": \"admin\",\"password\": \"1234\",\"password2\": \"1234\"}");
        $this->assertResponseStatusCodeSame(406);

    }
    public function testItRefusesBadBody()
    {
        $crawler = $this->testClient->request('POST', '/register',[],[],[],
            "{\"username\": \"admin\",\"password\": \"1234\"}");
        $this->assertResponseStatusCodeSame(406);

        $crawler = $this->testClient->request('POST', '/register',[],[],[],
            "");
        $this->assertResponseStatusCodeSame(406);
    }
    public function testItRefusesNotMatchingPasswords()
    {
        $crawler = $this->testClient->request('POST', '/register',[],[],[],
            "{\"username\": \"admin\",\"password\": \"1234\",\"password2\": \"1234\"}");
        $this->assertResponseStatusCodeSame(406);
    }
    public function testItRegisterUserIfAllConditionsMeet()
    {
        $crawler = $this->testClient->request('POST', '/register',[],[],[],
            "{\"username\": \"adminel\",\"password\": \"1234\",\"password2\": \"1234\"}");
        $this->assertResponseStatusCodeSame(201);
    }
}