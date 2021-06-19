<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogPost;
use App\Services\RequestValidator;
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
        $requestValidator = new RequestValidator($request);
        $requestValidator->init(["fullContent","shortContent"]);
        if($requestValidator->allValuesPassed()){
            $values = $requestValidator->allValuesPassed();
            $post = new BlogPost();
            $post->setFullContent($values["fullContent"])
                ->setShortContent($values["shortContent"]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return $this->json([
                'message' => 'Post added'
            ]);
        }
        return $this->json([
            'error' => 'Something is missing'
        ])->setStatusCode(401);
    }
    #[Route('/posts/{id}', name: 'editPostFully', methods: 'PUT')]
    #[IsGranted("ROLE_ADMIN")]
    public function editPostFully(BlogPost $blogPost, Request $request): Response
    {
        $requestValidator = new RequestValidator($request);
        $requestValidator->init(["fullContent","shortContent"]);
        if($requestValidator->allValuesPassed()){
            $values = $requestValidator->allValuesPassed();
            $blogPost->setFullContent($values["fullContent"])
                ->setShortContent($values["shortContent"]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($blogPost);
            $em->flush();
            return $this->json([
                'message' => 'Post edited'
            ]);
        }
        return $this->json([
            'error' => 'Something is missing'
        ])->setStatusCode(401);
    }
    #[Route('/posts/{id}', name: 'editPostPartially', methods: 'PATCH')]
    #[IsGranted("ROLE_ADMIN")]
    public function editPostPartially(BlogPost $blogPost,Request $request): Response
    {
        $requestValidator = new RequestValidator($request);
        $requestValidator->init(["fullContent","shortContent"]);
        if($requestValidator->atLeastOneValuesPassed()){
            $values = $requestValidator->atLeastOneValuesPassed();
            if(key_exists("fullContent",$values))
                $blogPost->setFullContent($values["fullContent"]);
             if(key_exists("shortContent",$values))
                 $blogPost->setShortContent($values["shortContent"]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($blogPost);
            $em->flush();
            return $this->json([
                'message' => 'Post edited'
            ]);
        }
        return $this->json([
            'error' => 'Something is missing'
        ])->setStatusCode(401);
    }
}
