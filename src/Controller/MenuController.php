<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    public function getRecentProjects(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findBy([], ['createdAt' => 'DESC'], 3);

        return $this->render('menu/_recent_projects.html.twig', [
            'projects' => 'MenuController',
        ]);
    }
}
