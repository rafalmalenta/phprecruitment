<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use App\Services\PostsDirector;
use App\Services\RequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class PostsController extends AbstractController
{
    /**
     * @return JsonResponse
     */
    #[Route('/posts', name: 'getPosts', methods: 'GET')]
    public function getPosts(Request $request): Response
    {
        /**
         * @var $postRepo BlogPostRepository
         */
        $page = $request->query->get("page") ?? 1;
        $limit = $request->query->get("limit") ?? 22;
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository(BlogPost::class);
        $posts = $postRepo->findAllPaginated($page,$limit);
        $maxPages = ceil($postRepo->postsCount()/$limit);
        $commentsCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []){
            return count($innerObject) > 0 ? "/posts/".$outerObject->getId()."/comments" : "";
        };
        return $this->json(
            [
                "meta"=>["page"=>$page, "limit"=>$limit, "total_pages"=>$maxPages],
                "posts"=>$posts
            ],
            200,
            [],
                [
                    AbstractNormalizer::CALLBACKS => [
                        'comments' => $commentsCallback,
                    ],
                    'groups'=> ["post_info"]],
            );
    }
    /**
     * @return JsonResponse
     */
    #[Route('/posts/{id}', name: 'getPost', methods: 'GET')]
    public function getPost(BlogPost $blogPost): Response
    {
        $commentsCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []){
            return count($innerObject) > 0 ? "/posts/".$outerObject->getId()."/comments" : "";
        };
        return $this->json($blogPost, 200,[],
            [
                AbstractNormalizer::CALLBACKS => [
                    'comments' => $commentsCallback,
                ],
                'groups'=>["post_info"]
            ]);
    }
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
