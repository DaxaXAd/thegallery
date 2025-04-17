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

#[Route('/register')]
class RegistrationController extends AbstractController
{
    #[Route('/', name: 'app_register')]
public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
{
    if ($this->getUser()) {
        return $this->redirectToRoute('app_posts_index');
    }

    $user = new User();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        if ($form->isValid()) {
            // Traitement du mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Image de profil par défaut
            if (!$user->getProfilePic()) {
                $user->setProfilePic('images/profil/profil.png');
            }

            // Création du slug
            $slugger = new AsciiSlugger();
            $slug = $slugger->slug($user->getUsername());
            $user->setSlug($slug);

            // Paramètres additionnels
            $user->setRoles(['ROLE_USER']);
            $user->setUpdatedAt(new \DateTimeImmutable());

            // Sauvegarde en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Message flash et redirection
            $this->addFlash('success', 'Inscription réussie ! Bienvenue sur TheGallery 🦊');
            
            // Connecter l'utilisateur directement
            // return $security->login($user, 'form_login', 'main');
          return $this->redirectToRoute('app_post_index');
        } else {
            // Afficher les erreurs de validation sans arrêter l'exécution
            $this->addFlash('danger', 'Le formulaire est invalide.');
            // Pour le débogage vous pouvez temporairement utiliser:
            dump($form->getErrors(true, true));
        }
    }

    return $this->render('registration/register.html.twig', [
        'registrationForm' => $form->createView(),
    ]);
}

}
