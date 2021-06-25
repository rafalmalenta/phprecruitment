<?php


namespace App\Tests\Services;


use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use App\Services\PostsDirector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class IntegrationPostDirectorTest extends KernelTestCase
{
    protected function setUp(): void
    {
        /**
         * @var $em EntityManagerInterface
         * @var $postRepo BlogPostRepository
         */
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();
        $postRepo =$em->getRepository(BlogPost::class);
        $posts = $postRepo->findAll();
        foreach ($posts as $post)
            $em->remove($post);
        $em->flush();
    }

    public function testItCreatesCorrectEntity()
    {
        /**
         * @var $director PostsDirector
         */
        $testArray = ["fullContent"=>"test content1","shortContent"=>"test content2"];
        $container = self::$kernel->getContainer();
        $director = $container->get(PostsDirector::class);
        $post = new BlogPost();
        $director->setPost($post);
        $director->setValuesFromArray($testArray);

        $this->assertSame("test content1",$post->getFullContent());
        $this->assertSame("test content2",$post->getShortContent());
    }
    public function testItEditCorrectEntity()
    {
        /**
         * @var $director PostsDirector
         */
        $testArray = ["fullContent"=>"test content1","shortContent"=>"test content2"];
        $container = self::$kernel->getContainer();
        $director = $container->get(PostsDirector::class);
        $post = new BlogPost();
        $post->setShortContent("should be overwritten")
            ->setFullContent("should be overwritten");
        $director->setPost($post);
        $director->setValuesFromArray($testArray);
        $this->assertSame("test content1",$post->getFullContent());
        $this->assertSame("test content2",$post->getShortContent());
    }
}