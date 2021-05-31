<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $user = $this->getUser() ?? new User();

        $registerForm = $this->createForm(RegistrationType::class, $user, [
            'action' => $this->generateUrl('security_register'),
        ]);

        return $this->render('home/index.html.twig', [
            'registerForm' => $registerForm->createView(),
        ]);
    }
}
