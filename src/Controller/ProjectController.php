<?php

namespace App\Controller;

use App\Entity\Project;
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
            'projects' => $projects,
            'userProjects' => $userProjects,
        ]);
    }

    #[Route('/project/{id}', name: 'project_show')]
    #[IsGranted('ROLE_USER')]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }
}
