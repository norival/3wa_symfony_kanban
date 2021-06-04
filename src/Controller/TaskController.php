<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/{project_slug}/task/{id}', name: 'task_show')]
    public function index(Task $task): Response
    {
        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }

    #[Route('/project/{slug}/task/new', name: 'task_new')]
    public function new(Request $request, Project $project): Response
    {
        $task = new Task();
        $task->setProject($project);

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task->setProject($project);

            $this->em->persist($task);
            $this->em->flush();

            return $this->redirectToRoute('project_show', ['slug' => $project->getSlug()]);
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/task/edit/{id}', name: 'task_edit')]
    public function edit(Request $request, Task $task): Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($task);
            $this->em->flush();

            return $this->redirectToRoute('project_show', ['slug' => $task->getProject()->getSlug()]);
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/api/task/status/{id}', name: 'task_api_set_status', methods: ['PATCH'])]
    public function apiSetStatus(Request $request, Task $task): JsonResponse
    {
        $content = \json_decode($request->getContent(), true);
        $task->setStatus((int) $content['status']);

        $this->em->flush();

        return $this->json([
            'id' => $task->getId(),
            'status' => $task->getStatus(),
        ]);
    }

    #[Route('/api/task/{id}/add-user', name: 'task_api_add_user', methods: ['PATCH'])]
    public function apiAddUser(Request $request, Task $task, UserRepository $userRepository): JsonResponse
    {
        $content = \json_decode($request->getContent(), true);
        $user = $userRepository->find($content['user']);

        if ($user) {
            $task->addAssignee($user);

            $this->em->flush();
        }

        return $this->json($user, context: ['groups' => 'display']);
    }
}
