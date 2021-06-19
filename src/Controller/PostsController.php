<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    #[Route('/posts', name: 'getPosts', methods: 'GET')]
    public function getPosts(): Response
    {
        return $this->json([
            'message' => '1Welcome to your new controller!',
            'path' => 'src/Controller/PostsController.php',
        ]);
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
