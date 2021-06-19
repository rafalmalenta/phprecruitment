<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogPost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PostsController extends AbstractController
{
    #[Route('/posts', name: 'getPosts', methods: 'GET')]
    public function getPosts(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(BlogPost::class)->findAll();
        return $this->json($posts, 200);
    }
    #[Route('/posts/{id}', name: 'getPost', methods: 'GET')]
    public function getPost(BlogPost $blogPost): Response
    {
        return $this->json($blogPost, 200);
    }

    #[Route('/posts', name: 'addPost', methods: 'POST')]
    #[IsGranted("ROLE_ADMIN")]
    public function addPost(Request $request): Response
    {
        $body = json_decode($request->getContent(), true);
        if(!array_key_exists('fullContent', $body) or !array_key_exists('shortContent', $body) )
            return $this->json([
                'error' => 'Something is missing'
            ]);
        $fullContent = $body['fullContent'];
        $shortContent = $body['shortContent'];
        if($fullContent and $shortContent) {
            $post = new BlogPost();
            $post->setFullContent($fullContent)
                ->setShortContent($shortContent);
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return $this->json([
                'message' => 'Post added'
            ]);
        }
        return $this->json([
            'error' => 'Something is missing'
        ]);
    }
}
