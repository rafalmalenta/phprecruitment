<?php


namespace App\Controller;


use App\Entity\BlogPost;
use App\Services\PostsDirector;
use App\Services\RequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsActionsController extends AbstractController
{
    /**
     * @return JsonResponse
     */
    #[Route('/posts', name: 'addPost', methods: 'POST')]
    #[IsGranted("ROLE_ADMIN")]
    public function addPost(Request $request, EntityManagerInterface $em): Response
    {
        $requestValidator = new RequestValidator($request);
        $requestValidator->setRequestPattern(["fullContent","shortContent"]);
        if($requestValidator->allValuesPassed()){
            $values = $requestValidator->getValidValues();
            $director = new PostsDirector(new BlogPost(),$em);
            $director->setValuesFromArray($values);
            return $this->json([
                'message' => 'Post added'
            ]);
        }
        return $this->json([
            'error' => 'Something is missing'
        ])->setStatusCode(401);
    }
    /**
     * @return JsonResponse
     */
    #[Route('/posts/{id}', name: 'editPostCompletely', methods: 'PUT')]
    #[IsGranted("ROLE_ADMIN")]
    public function editPostCompletely(BlogPost $blogPost, Request $request, EntityManagerInterface $em): Response
    {
        $requestValidator = new RequestValidator($request);
        $requestValidator->setRequestPattern(["fullContent","shortContent"]);
        if($requestValidator->allValuesPassed()){
            $values = $requestValidator->getValidValues();
            $director = new PostsDirector($blogPost, $em);
            $director->setValuesFromArray($values);
            return $this->json([
                'message' => 'Post edited'
            ], 200);
        }
        return $this->json([
            'error' => 'Something is missing'
        ])->setStatusCode(401);
    }
    /**
     * @return JsonResponse
     */
    #[Route('/posts/{id}', name: 'editPostPartially', methods: 'PATCH')]
    #[IsGranted("ROLE_ADMIN")]
    public function editPostPartially(BlogPost $blogPost, Request $request, EntityManagerInterface $em): Response
    {
        $requestValidator = new RequestValidator($request);
        $requestValidator->setRequestPattern(["fullContent","shortContent"]);
        if($requestValidator->atLeastOneValuesPassed()){
            $values = $requestValidator->getValidValues();
            $director = new PostsDirector($blogPost, $em);
            $director->setValuesFromArray($values);
            return $this->json([
                'message' => 'Post edited'
            ]);
        }
        return $this->json([
            'error' => 'Something is missing'
        ])->setStatusCode(401);
    }

}