<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/project/{project_slug}/task/new', name: 'task_new')]
    #[ParamConverter(
        [
            'value' => 'project',
            'class' => Project::class,
            'options' => ['mapping'=>['project_slug' => 'slug']]
        ]
    )]
    public function new(Request $request, Project $project, ProjectRepository $projectRepository, Task $task = null): Response
    {
        if (!$task) {
            $task = new Task();
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /* $project = $projectRepository->find($form->get('project')->getData()); */
            $task->setProject($project);

            $this->em->persist($project);
            $this->em->flush();

            return $this->redirectToRoute('project_show', ['project_slug' => $project->getSlug()]);
        }
    }
}
