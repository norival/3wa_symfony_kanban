<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'account')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('account/index.html.twig', [
            'user' => $user,
        ]);
    }
}
