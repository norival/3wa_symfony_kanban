<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\ProjectType;
use App\Form\TaskType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

class ProjectController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer
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

        if ($project && $project->getAdmin() !== $user) {
            throw new AccessDeniedException('Vous ne pouvez pas modifier un projet qui ne vous appartient pas');
        }

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
            'form'    => $form->createView(),
            'project' => $project,
        ]);
    }

    #[Route('/project/{slug}', name: 'project_show')]
    #[IsGranted('ROLE_USER')]
    public function show(Project $project): Response
    {
        $tasks = $project->getTasks();

        $statuses = [
            'A faire'  => Task::STATUS_TODO,
            'En cours' => Task::STATUS_ONGOING,
            'Terminé'  => Task::STATUS_DONE,
        ];

        return $this->render('project/show.html.twig', [
            'statuses'        => $statuses,
            'project'         => $project,
            'tasks'           => $tasks,
            'isUserInProject' => $project->isUserInProject($this->getUser()),
        ]);
    }

    #[Route('/api/{id}/user/search', name: 'project_api_search_user',  methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function apiSearchUser(Project $project, Request $request, UserRepository $userRepository): JsonResponse
    {
        /* $content = \json_decode($request->getContent(), true); */
        $search = $request->get('q');

        // TODO select only users that are not already in the project
        $users = $userRepository->findByName($search);
        $users = array_filter($users, fn($user) => $user !== $project->getAdmin());

        return $this->json($users, context: ['groups' => 'autocomplete']);
    }
}
