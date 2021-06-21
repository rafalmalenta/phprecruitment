<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Services\RequestValidator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class CommentsController extends AbstractController
{
    #[Route('/comments', name: 'allComments', methods: 'GET')]
    public function allComments(Request $request): Response
    {
        $page = $request->query->get("page") ?? 1;
        $limit = $request->query->get("limit") ?? 22;
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findAllPaginated($page, $limit);
        $dateCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $innerObject instanceof \DateTime ? $innerObject->format('Y-m-d H:i') : '';
        };

        return $this->json(
            ['comments' => $comments],
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
    #[Route('/posts/{id}/comments', name: 'getPostComments', methods: 'GET')]
    public function getPostComments(int $id, Request $request): Response
    {
        $dateCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $innerObject instanceof \DateTime ? $innerObject->format('Y-m-d H:i') : '';
        };
        $page = $request->query->get("page") ?? 1;
        $limit = $request->query->get("limit") ?? 22;
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findAllPaginatedWithOwnerId($id,$page, $limit);
        if(!$comments){
            return $this->json(
                [
                    'error' => "resource don't exist",
                ],
                404,
               );
        }
        return $this->json(
            [
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
    #[Route('/comments', name: 'createComment', methods: 'POST')]
    #[IsGranted("ROLE_USER")]
    public function createComment(Request $request): Response
    {
        $requestValidator = new RequestValidator($request);
        $requestValidator->init(["postId","comment"]);
        if($requestValidator->allValuesPassed()){
            $values = $requestValidator->allValuesPassed();
            $comment= new Comment();
            $postId = $values["postId"];
            $post = $this->getDoctrine()->getRepository(BlogPost::class)->findOneBy(['id'=>"$postId"]);
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
    #[Route('/comments/{id}', name: 'publishComment', methods: 'PATCH')]
    #[IsGranted("ROLE_ADMIN")]
    public function publishComment(Comment $comment, Request $request): Response
    {
        $requestValidator = new RequestValidator($request);
        $requestValidator->init(["publish"]);
        if($requestValidator->allValuesPassed()){
            $values = $requestValidator->allValuesPassed();
            if($values["publish"] == true){
                $comment->setPublishedAt(new \DateTime());
                $em= $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();
                return $this->json(
                    ['message'=>"successfully published"],
                    202);
            }
        }
        return $this->json(
            ['message'=>"something went wrong"],
            403,
        );
    }
    #[Route('/comments/{id}', name: 'deleteComment', methods: 'DELETE')]
    #[IsGranted("ROLE_ADMIN")]
    public function deleteComment(Comment $comment): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        return $this->json(
            ['message'=>"successfully deleted"],
            202);
    }
}
