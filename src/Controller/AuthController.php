<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    /* Sign In route for website */
    #[Route(path: '/sign-in', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, LoggerInterface $logger): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // Logger les tentatives de connexion
        $logger->info('Tentative de connexion', [
            'username' =>  $authenticationUtils,
            'error' => $error ? $error->getMessage() : null,
        ]);

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /* Sign Up route for website */
    #[Route('/sign-up', name: 'app_sign_up')]
    public function signUp(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $cgu = $form->get('cguAccepted')->getData();

            $user->setPassword($userPasswordHasher->hashPassword($user, $password));
            $user->setApiActivated(0);
            $user->setCguAccepted($cgu);
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            //return $security->login($user, 'form_login', 'main');
            return $this->redirectToRoute('app_home');

        }

        return $this->render('auth/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
