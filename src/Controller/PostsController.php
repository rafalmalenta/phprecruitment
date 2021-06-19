<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PostsController extends AbstractController
{
    #[Route('/posts', name: 'getPosts', methods: 'GET')]
    #[IsGranted('ROLE_USER')]
    public function getPosts(): Response
    {
        //dd($this->getUser());
        //$this->denyAccessUnlessGranted('AUTHENTICATED_FULLY');
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(BlogPost::class)->findAll();
        return $this->json($posts, 200);
    }

    #[Route('/posts', name: 'addPost', methods: 'POST')]
    public function addPost(): Response
    {

        return $this->json([
            'message' => '2Welcome to your new controller!',
            'path' => 'src/Controller/PostsController.php',
        ]);
    }
}
