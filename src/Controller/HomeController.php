<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private PaginatorInterface $paginator
    ) { }

    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        $dql = 'SELECT a FROM App\Entity\Project a';
        $query = $this->em->createQuery($dql);

        $pagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('home/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
