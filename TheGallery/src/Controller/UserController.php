<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Post;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\PostsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Controller\SecurityController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/user')]
final class UserController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/user/{id}', name: 'app_user_index', methods: ['GET'])]
    public function index(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
                }
        return $this->render('user/index.html.twig', [
            'user' => $user
            
        ]);
    }




    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            if (!$user->getProfilePic()) {
                $user->setProfilePic('images/profil/profil.png');
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }




    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }




    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newPassword = $form->get('password')->getData();
            if (!empty($newPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $profilePicture = $form->get('profilePicture')->getData();
            if ($profilePicture) {
                
                $pictureFilename = uniqid(). '.' .$profilePicture->guessExtension();
                $profilePicture->move(
                    $this->getParameter('profile_pictures_directory'),
                    $pictureFilename
                );
                $user->setProfilePic('images/profil/' . $pictureFilename);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {

            if ($user->getProfilePic()) {
                $profilePicturePath = $this->getParameter('kernel.project_dir') . '/public' . $user->getProfilePic();
                if (file_exists($profilePicturePath)) {
                    unlink($profilePicturePath);
                }
            }

            $entityManager->remove($user);
            $entityManager->flush();

            $request->getSession()->invalidate();
            $this->container->get('security.token_storage')->setToken(null);
            
            return $this->redirectToRoute('app_logout');
        }

        return $this->redirectToRoute('home_page', [], Response::HTTP_SEE_OTHER);
    }


    

    #[Route('/profile/{id}', name: 'app_profile', methods: ['GET'])]
    public function profile(int $id, User $user, ManagerRegistry $doctrine): Response
    {
        // Fetch the user by id
        $user = $this->doctrine->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Fetch the images related to the user.
        // Option 1: If your User entity has a OneToMany relation named "images", then:
        // $images = $user->getImages();
        //
        // Option 2: Otherwise, query the Image repository:
        $posts = $this->doctrine->getRepository(Post::class)->findBy(['id_user' => $user]);

        // Render a Twig template with the user and images
        return $this->render('user/profile.html.twig', [
            'user'   => $user,
            'posts' => $posts,
        ]);
    }
}
