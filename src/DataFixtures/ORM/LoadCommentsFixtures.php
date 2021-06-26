<?php


namespace App\DataFixtures\ORM;


use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadCommentsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $fixUser = $manager->getRepository(User::class)->findAll();
        $fixPost = $manager->getRepository(BlogPost::class)->findAll();
        foreach ($fixPost as $post){
            foreach ($fixUser as $user){
                $comment = new Comment();
                $comment->setPost($post)
                    ->setUser($user)
                    ->setContent("random string");
                $manager->persist($comment);
            }
            $manager->flush();
        }
    }
    public function getDependencies(): array
    {
        return [
            LoadUserFixtures::class,
            LoadPostFixtures::class
        ];

    }

}