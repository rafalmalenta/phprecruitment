<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentsController extends AbstractController
{
    #[Route('/comments', name: 'allComments',methods: 'GET')]
    public function allComments(Request $request): Response
    {
        $page = $request->query->get("page") ?? 1;
        $limit = $request->query->get("limit") ?? 22;
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findAllPaginated($page, $limit);
        return $this->json(
            ['comments' => $comments],
      200,
            [],
            ['groups'=> ["comment_info"]]
        );
    }
    #[Route('/posts/{id}/comments', name: 'postComments',methods: 'GET')]
    public function postComments(int $id, Request $request): Response
    {
        $page = $request->query->get("page") ?? 1;
        $limit = $request->query->get("limit") ?? 22;
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findAllPaginatedWithOwnerId($id,$page, $limit);
        return $this->json(
            [
                'comments' => $comments,
            ],
            200,
            [],
            ['groups'=> ["comment_info"]]
        );
    }
}
