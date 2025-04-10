<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_posts_index');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        
   
        
        if ($form->isSubmitted()) {
            dump('Form valid:', $form->isValid());
            dump($form->getErrors(true, true)); // Montre les erreurs précises


            if($form->isValid()) {
                dump($form->getData());
                $plainPassword = $form->get('plainPassword')->getData();
    
                // encode the plain password
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
    
                if (!$user->getProfilePic()) {
                    $user->setProfilePic('images/profil/profil.png');
                }
    
                $user->setRoles(['ROLE_USER']);
    
                $entityManager->persist($user);
                $entityManager->flush();
                // do anything else you need here, like send an email
    
                return $security->login($user, 'form_login', 'main');
                // $security->login($user);
    
                // return $this->redirectToRoute('app_posts_index', ['id' => $user->getId()]);
            }
          
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
