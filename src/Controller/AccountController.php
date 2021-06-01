<?php

namespace App\Controller;

use App\Form\UserType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class AccountController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private FileUploader $fileUploader
    ) { }

    #[Route('/account', name: 'account')]
    public function index(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('account/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/account/edit', name: 'account_edit')]
    public function edit(Request $request): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $profilePicture = $form->get('profilePicture')->getData();
            if ($profilePicture) {
                $filename = $this->fileUploader->upload($profilePicture);
                $user->setProfilePicture($filename);
            }

            $this->em->flush();

            return $this->redirectToRoute('account');
        }

        return $this->render('account/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
