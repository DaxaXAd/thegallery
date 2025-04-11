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
use Symfony\Component\String\Slugger\AsciiSlugger;


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
                
            if (!$form->isValid()) {
                dd($form->getErrors(true, true)); 
            }

            // dump($form->getErrors(true, true)); 

            
            if ($form->isValid()) {
                // dd($form->getErrors(true, true)); 


                $plainPassword = $form->get('plainPassword')->getData();
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

                if (!$user->getProfilePic()) {
                    $user->setProfilePic('images/profil/profil.png');
                }

                $user->setRoles(['ROLE_USER']);
                $user->setUpdatedAt(new \DateTimeImmutable());

                // Création du slug à partir du username
                $slugger = new AsciiSlugger();
                $slug = $slugger->slug($user->getUsername());
                $user->setSlug($slug);

                $user->setRoles(['ROLE_USER']);
                $user->setUpdatedAt(new \DateTimeImmutable());
                dd($entityManager);
                $entityManager->persist($user);
                $entityManager->flush();


                $this->addFlash('success', 'Inscription réussie ! Bienvenue sur TheGallery 🦊');
                return $this->redirectToRoute('app_posts_index');
            }

            $this->addFlash('danger', 'Le formulaire est invalide.');
            return $this->redirectToRoute('app_register');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    #[Route('/test-user', name: 'test_user')]
    public function test(EntityManagerInterface $em): Response
    {
        $user = new User();
        $user->setEmail('fox@test.com');
        $user->setPassword('testpass');
        $user->setRoles(['ROLE_USER']);
        $user->setUsername('FoxTest');
        $user->setSlug('foxtest');
        $user->setProfilePic('images/profil/profil.png');
        $user->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($user);
        $em->flush();

        return new Response("User ajouté !");
    }

    #[Route('/debug/doctrine', name: 'debug_doctrine')]
    public function debugDoctrine(EntityManagerInterface $em): Response
    {
        $meta = $em->getMetadataFactory()->getAllMetadata();
        $entityNames = array_map(fn($m) => $m->getName(), $meta);
        return new Response('<pre>' . print_r($entityNames, true) . '</pre>');
    }
}
// class RegistrationController extends AbstractController
// {
//     #[Route('/register', name: 'app_register')]
//     public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
//     {
//         if ($this->getUser()) {
//             return $this->redirectToRoute('app_posts_index');
//         }

//         $user = new User();
//         $form = $this->createForm(RegistrationFormType::class, $user);
//         $form->handleRequest($request);



//         if ($form->isSubmitted()) {
//             // dump('Form valid:', $form->isValid());
//             // dump($form->getErrors(true, true)); // Montre les erreurs précises


//             if($form->isValid()) {
//                 // dump($form->getData());
//                 $plainPassword = $form->get('plainPassword')->getData();
//                 // dump($plainPassword);
//                 // encode the plain password
//                 $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
//                 // dump($user->getPassword());
//                 if (!$user->getProfilePic()) {
//                     $user->setProfilePic('images/profil/profil.png');
//                 }

//                 $user->setRoles(['ROLE_USER']);

//                 $entityManager->persist($user);
//                 $entityManager->flush();
//                 // do anything else you need here, like send an email

//                 return $security->login($user, 'form_login', 'main');
//                 // $security->login($user);

//                 // return $this->redirectToRoute('app_posts_index', ['id' => $user->getId()]);
//                  // ou autre route logique
//             }
//             return $this->redirectToRoute('app_post_index');
//         }

//         return $this->render('registration/register.html.twig', [
//             'registrationForm' => $form->createView(),
//         ]);
//     }
// }
