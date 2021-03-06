<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
    public function index(Request $request, MailerInterface $mailer): Response
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

            $mail = new Email();
            $mail->from('test@norival.dev')
                 ->to($user->getEmail())
                 ->subject('Activate your account')
                 ->text('Click on this link: ' . $this->generateUrl('security_activate', [
                     'id' => $user->getId(),
                     'token' => $user->getActivationToken(),
                 ], UrlGeneratorInterface::ABSOLUTE_URL))
             ;
            $mailer->send($mail);

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'security_logout')]
    public function logout(): void
    {
    }

    #[Route('/account/activate/{id}/{token}', name: 'security_activate')]
    public function activate(User $user, string $token): Response
    {
        if ($user->getIsActif()) {
            return $this->redirectToRoute('security_login');
        }

        if ($user->getActivationToken() === $token) {
            $user->setIsActif(true);

            $this->em->flush();
        }

        return $this->render('security/activation.html.twig', [
            'actif' => $user->getIsActif(),
        ]);
    }

    #[Route('/login', name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
}
