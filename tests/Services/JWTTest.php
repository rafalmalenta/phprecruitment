<?php


namespace App\Tests\Services;


use App\Services\JWTService;
use DateTime;
use PHPUnit\Framework\TestCase;

class JWTTest extends testCase
{
    public function testItCreatesValidToken()
    {
        $jwt = new JWTService();
        $token = $jwt->generateToken("Testowanko");

        $this->assertSame("Testowanko", $jwt->getToken($token));
    }
    public function testTokenExpiresAfter600Seconds()
    {
        $jwt = new JWTService();
        $token = $jwt->generateToken("Testowanko");
        $jwt->addTime(6000);
        $jwt->verifyToken($token);
        $this->assertSame(false, $jwt->verifyToken($token));
    }
}