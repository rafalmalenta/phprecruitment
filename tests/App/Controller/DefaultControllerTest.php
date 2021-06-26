<?php


namespace App\Tests\App\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $testClient;

    public function setUp(): void
    {
        $this->testClient = static::createClient();
        $container = self::$kernel->getContainer();
    }
    public function testItReturnsErrorOnBadRoute()
    {
        $crawler = $this->testClient->request('GET', '/pofsdfsts',[],[],['ACCEPT'=>'Application/json']);
        $this->assertResponseStatusCodeSame(404);

    }
}