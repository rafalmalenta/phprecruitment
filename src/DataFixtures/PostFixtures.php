<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class LoadPostFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $post = [
            'short'=>"lfdfsfdsdfsfsddfs",
            'full'=>"lfdfsfdhgfhgfhfghfgsdfsfsddfs"
        ];
        for($i = 1; $i <= 10; $i++) {
            $fixture = new BlogPost();
            $fixture->setShortContent($post['short'])
                ->setFullContent($post['full']);
            $manager->persist($fixture);
        }

        $manager->flush();
    }
}
