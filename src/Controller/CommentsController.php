<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Repository\CommentRepository;
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

class CommentsController extends AbstractController
{
    #[Route('/comments', name: 'allComments', methods: 'GET')]
    /**
     * @return JsonResponse
     */
    public function allComments(Request $request, EntityManagerInterface $em): Response
    {
        /**
         * @var $commentsRepo CommentRepository
         */
        $page = $request->query->get("page") ?? 1;
        $limit = $request->query->get("limit") ?? 22;
        $commentsRepo = $em->getRepository(Comment::class);
        $comments = $commentsRepo->findAllPaginated($page, $limit);
        $maxPages = ceil($commentsRepo->commentsCount()/$limit);
        $dateCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $innerObject instanceof \DateTime ? $innerObject->format('Y-m-d H:i') : '';
        };
        return $this->json(
            [
                "meta"=>["page"=>$page, "limit"=>$limit, "total_pages"=>$maxPages],
                'comments' => $comments,
            ],
      200,
            [],
            [
                AbstractNormalizer::CALLBACKS => [
                    'publishedAt' => $dateCallback,
                ],
                'groups'=> ["comment_info"]
            ]
        );
    }
    /**
     * @return JsonResponse
     */
    #[Route('/comments/{id}', name: 'comment', methods: 'GET')]
    public function comment(Comment $comment): Response
    {
        $dateCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $innerObject instanceof \DateTime ? $innerObject->format('Y-m-d H:i') : '';
        };
        return $this->json(
            ['comments' => $comment],
            200,
            [],
            [
                AbstractNormalizer::CALLBACKS => [
                    'publishedAt' => $dateCallback,
                ],
                'groups'=> ["comment_info"]
            ]
        );
    }
    /**
     * @return JsonResponse
     */
    #[Route('/posts/{id}/comments', name: 'getPostComments', methods: 'GET')]
    public function getPostComments(int $id, Request $request, CommentRepository $commentsRepo): Response
    {
        $dateCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $innerObject instanceof \DateTime ? $innerObject->format('Y-m-d H:i') : '';
        };
        $page = $request->query->get("page") ?? 1;
        $limit = $request->query->get("limit") ?? 22;
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findAllPaginatedWithOwnerId($id,$page, $limit);
        $maxPages = ceil($commentsRepo->commentsCount()/$limit);
        if(!$comments){
            return $this->json(['error' => "resource don't exist"],404);
        }
        return $this->json(
            [
                "meta"=>["page"=>$page, "limit"=>$limit, "total_pages"=>$maxPages],
                'comments' => $comments,
            ],
            200,
            [],
            [
                AbstractNormalizer::CALLBACKS => [
                    'publishedAt' => $dateCallback,
                ],
                'groups'=> ["comment_info"]]
        );
    }
    /**
     * @return JsonResponse
     */
    #[Route('/comments', name: 'createComment', methods: 'POST')]
    #[IsGranted("ROLE_USER")]
    public function createComment(Request $request): Response
    {
        $requestValidator = new RequestValidator($request);
        $requestValidator->setRequestPattern(["postId","comment"]);
        if($requestValidator->allValuesPassed()){
            $values = $requestValidator->getValidValues();
            $postId = $values["postId"];
            $post = $this->getDoctrine()->getRepository(BlogPost::class)->findOneBy(['id'=>"$postId"]);
            if (!$post)
                return $this->json(
                    ['message'=>"something went wrong"],
                    403,
                );
            $comment= new Comment();
            $comment->setPost($post)
                ->setContent($values["comment"])
                ->setUser($this->getUser());
            $em= $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            return $this->json(
                ['message'=>"success"],
                200,
            );
        }
        return $this->json(
            ['message'=>"something went wrong"],
            403,
        );
    }
    /**
     * @return JsonResponse
     */
    #[Route('/comments/{id}', name: 'editComment', methods: 'PUT')]
    public function editComment(Comment $comment, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('OWNS', $comment);
        $requestValidator = new RequestValidator($request);
        $requestValidator->setRequestPattern(["comment"]);
        if($requestValidator->allValuesPassed()){
            $values = $requestValidator->getValidValues();
            $comment->setContent($values['comment']);
            $em->flush();
            return $this->json([
                "message"=>"successfully edited"
            ],
            200);
        }
    }
    /**
     * @return JsonResponse
     */
    #[Route('/comments/{id}', name: 'publishComment', methods: 'PATCH')]
    #[IsGranted("ROLE_ADMIN")]
    public function publishComment(Comment $comment, Request $request): Response
    {
        $requestValidator = new RequestValidator($request);
        $requestValidator->setRequestPattern(["publish"]);
        if($requestValidator->allValuesPassed()){
            $values = $requestValidator->getValidValues();
            if($values["publish"] == true){
                $comment->setPublishedAt(new \DateTime());
                $em= $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();
                return $this->json(
                    ['message'=>"successfully published"],
                    200);
            }
        }
        return $this->json(
            ['message'=>"something went wrong"],
            403,
        );
    }
    /**
     * @return JsonResponse
     */
    #[Route('/comments/{id}', name: 'deleteComment', methods: 'DELETE')]
    public function deleteComment(Comment $comment, EntityManagerInterface $em): Response
    {
        if($this->isGranted("ROLE_ADMIN") or $this->isGranted("OWNS",$comment)) {
            $em->remove($comment);
            $em->flush();
            return $this->json(
                ['message' => "successfully deleted"],
                200);
        }
        return $this->json(
            ['error' => "something wrong"],
            200);
    }
}
