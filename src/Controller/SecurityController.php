<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private FileUploader $fileUploader,
        private UserPasswordEncoderInterface $passwordEncoder
    ) {
    }

    #[Route('/register', name: 'security_register')]
    public function index(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $form->get('password')->getData()
            ));

            $user->setIsActif(false);

            $profilePicture = $form->get('profilePicture')->getData();
            if ($profilePicture) {
                $filename = $this->fileUploader->upload($profilePicture);
                $user->setProfilePicture($filename);
            }

            $user->setActivationToken(
                bin2hex(openssl_random_pseudo_bytes(32))
            );

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/login', name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/logout', name: 'security_logout')]
    public function logout(): void
    {
    }

    #[Route('/account/activate/{id}/{token}', name: 'security_activate')]
    public function activate(User $user, string $token): void
    {
        if ($user->setIsActif()) {
            return $this->redirectToRoute('security_login');
        }

        if ($user->getActivationToken() === $token) {
            $user->setIsActif(true);
        }

        return $this->render('security/activation.html.twig', [
        ]);
    }
}
