<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    ) { }

    #[Route('/task/{id}/comment/new', name: 'comment_api_new', methods: ['POST'])]
    public function index(Request $request, Task $task): JsonResponse
    {
        $content = \json_decode($request->getContent(), true);

        if (!$content['content']) {
            return $this->json('nothing');
        }

        $user = $this->getUser();

        $comment = new Comment();
        $comment->setUser($user);
        $comment->setTask($task);
        $comment->setContent($content['content']);

        $this->em->persist($comment);
        $this->em->flush();

        return $this->json($comment, context: ['groups' => 'display']);
    }
}
