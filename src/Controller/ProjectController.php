<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\ProjectType;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    ) { }

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

    #[Route('/project/new', name: 'project_new')]
    #[Route('/project/{slug}/new', name: 'project_edit')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, Project $project = null): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$project) {
            $project = new Project();
        }

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setAdmin($user);

            $this->em->persist($project);
            $this->em->flush();

            return $this->redirectToRoute('project_show', [
                'slug' => $project->getSlug(),
            ]);
        }

        return $this->render('project/edit.html.twig', [
            'form' => $form->createView(),
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
