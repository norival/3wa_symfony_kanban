<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    #[Route('/project', name: 'project_list')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $projects = $user->getProjects();
        $userProjects = $user->getUserProjects();

        return $this->render('project/index.html.twig', [
            'projects'     => $projects,
            'userProjects' => $userProjects,
        ]);
    }

    #[Route('/project/{slug}', name: 'project_show')]
    #[IsGranted('ROLE_USER')]
    public function show(Project $project): Response
    {
        $tasks = $project->getTasks();

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task, [
            'action' => $this->generateUrl('task_new', ['slug' => $project->getSlug()]),
        ]);

        $statuses = [
            'A faire'  => Task::STATUS_TODO,
            'En cours' => Task::STATUS_ONGOING,
            'Terminé'  => Task::STATUS_DONE,
        ];

        return $this->render('project/show.html.twig', [
            'form'     => $form->createView(),
            'statuses' => $statuses,
            'project'  => $project,
            'tasks'    => $tasks,
        ]);
    }
}
