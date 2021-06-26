<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class PostsViewController extends AbstractController
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

}
