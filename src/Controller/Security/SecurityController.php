<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authUtils): Response
    {

        return $this->render('Security/login.html.twig', [
            'error' => $authUtils->getLastAuthenticationError(),
            'lastUsername' => $authUtils->getLastUsername(),
        ]);
    }
    #[Route('/register', name: 'app.register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        $user = new User();


        $form = $this->createForm(UserType::class, $user);
        $form->handlerequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // on hash le mdp
            $user->setPassword(
                $hasher->hashPassword($user, $form->get('password')->getData())
            );

            // On envoie le user en BDD
            $em->persist($user);
            $em->flush();

            // On définit un message flashdd($request);

            $this->addFlash('success','Vous êtes bien inscrits à notre applicaiton symfony');

            return $this->redirectToRoute('app.login');
        }

        return $this->render('Security/register.html.twig', [
            'form' => $form,

        ]);
    }
}
