<?php declare(strict_types=1);

namespace App\Services;

use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;

class PostsDirector
{
    private BlogPost $post;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function setPost(BlogPost $post)
    {
        $this->post = $post;
    }

    public function setValuesFromArray(array $nameValArray): void
    {
        foreach ($nameValArray as $name =>$value){
            $this->setValueFromName([$name=>$value]);
        }
        $this->entityManager->persist($this->post);
        $this->entityManager->flush();
    }

    public function setValueFromName(array $nameValPair): int
    {
        switch (key($nameValPair)){
            case 'fullContent':
                $this->post->setFullContent($nameValPair['fullContent']);
                return 0;
            case 'shortContent':
                $this->post->setShortContent($nameValPair['shortContent']);
                return 0;
        }
    }
}