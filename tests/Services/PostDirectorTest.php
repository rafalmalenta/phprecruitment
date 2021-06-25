<?php


namespace App\Tests\Services;


use App\Entity\BlogPost;
use App\Services\PostsDirector;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PostDirectorTest extends TestCase
{
    private EntityManagerInterface $mockManager;

    private BlogPost $mockPost;

    public function setUp(): void
    {
        $this->mockManager = $this->createMock(EntityManagerInterface::class);
        $this->mockPost = $this->createMock(BlogPost::class);
    }

    public function testItExecutesCorrectlyForFullArray()
    {
        $testArray = ["fullContent"=>"test content1","shortContent"=>"test content2"];
        $director = new PostsDirector($this->mockPost, $this->mockManager);
        $this->mockManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(BlogPost::class));

        $this->mockPost->expects($this->once())
            ->method('setFullContent')
            ->with(('test content1'));
        $this->mockPost->expects($this->once())
            ->method('setShortContent')
            ->with(('test content2'));

        $director->setValuesFromArray($testArray);
    }

    public function testItExecutesCorrectlyForPartialArray()
    {
        $testArray = ["fullContent"=>"test content1"];
        $director = new PostsDirector($this->mockPost, $this->mockManager);
        $this->mockManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(BlogPost::class));

        $this->mockPost->expects($this->once())
            ->method('setFullContent')
            ->with(('test content1'));
        $this->mockPost->expects($this->never())
            ->method('setShortContent')
            ->with(('test content2'));

        $director->setValuesFromArray($testArray);
    }
}