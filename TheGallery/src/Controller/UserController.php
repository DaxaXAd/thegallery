<?php

namespace App\Controller;

// Entity imports
use App\Entity\User;
use App\Entity\Post;
use App\Entity\Image;

// Form imports
use App\Form\UserType;

// Repository imports
use App\Repository\UserRepository;
use App\Repository\PostsRepository;
use App\Repository\ImageRepository;
use App\Repository\LikeRepository;

// Symfony component imports
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * Controller managing all user-related operations
 */
#[Route('/user')]
final class UserController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Displays user index page with their posts and likes
     */
    #[Route('/user/{slug}', name: 'app_user_index', methods: ['GET'])]
    public function index(int $id, UserRepository $userRepository, LikeRepository $likeRepository, string $slug): Response
    {
        // Find user by slug or throw 404
        $user = $userRepository->findOneBy(['slug' => $slug]);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Get all posts for this user
        $posts = $user->getPosts();

        // Calculate like count for each post
        $likeCount = [];
        foreach ($posts as $post) {
            $likeCount[$post->getId()] = $likeRepository->totalLike($post->getId());
        }

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'likeCount' => $likeCount,
        ]);
    }

    /**
     * Creates a new user
     */
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set default user role
            $user->setRoles(['ROLE_USER']);

            // Set default profile picture if none provided
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

    /**
     * Shows detailed user information
     */
    #[Route('/{slug}', name: 'app_user_show', methods: ['GET'])]
    public function show(UserRepository $userRepository, LikeRepository $likeRepository, string $slug): Response
    {
        // Find user by slug or throw 404
        $user = $userRepository->findOneBy(['slug' => $slug]);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $posts = $user->getPosts();

        // Calculate likes for each post
        $likeCount = [];
        foreach ($posts as $post) {
            $likeCount[$post->getId()] = $likeRepository->totalLike($post->getId());
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'likeCount' => $likeCount,
        ]);
    }

    /**
     * Edits user information including password and profile picture
     */
    #[Route('/{slug}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, string $slug): Response
    {
        // Find user by slug or throw 404
        $user = $userRepository->findOneBy(['slug' => $slug]);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle password update if provided
            $newPassword = $form->get('password')->getData();
            if (!empty($newPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            // Handle profile picture upload
            $profilePicture = $form->get('profile_pic')->getData();
            if ($profilePicture) {
                $pictureFilename = uniqid() . '.' . $profilePicture->guessExtension();
                try {
                    // Move uploaded file to profile pictures directory
                    $profilePicture->move(
                        $this->getParameter('profile_pictures_directory'),
                        $pictureFilename
                    );
                    $user->setProfilePic('images/profil/' . $pictureFilename);
                } catch (FileException $e) {
                    throw new \Exception($e->getMessage());
                }
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_show', ['slug' => $user->getSlug()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a user account and their profile picture
     */
    #[Route('/{slug}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, string $slug, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneBy(['slug' => $slug]);
        if ($this->isCsrfTokenValid('delete' . $user->getSlug(), $request->getPayload()->getString('_token'))) {
            // Delete profile picture file if it exists
            if ($user->getProfilePic()) {
                $profilePicturePath = $this->getParameter('kernel.project_dir') . '/public' . $user->getProfilePic();
                if (file_exists($profilePicturePath)) {
                    unlink($profilePicturePath);
                }
            }

            // Remove user and logout
            $entityManager->remove($user);
            $entityManager->flush();

            // Invalidate session and clear security token
            $request->getSession()->invalidate();
            $this->container->get('security.token_storage')->setToken(null);

            return $this->redirectToRoute('app_logout');
        }

        return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Shows user profile with their images and posts
     */
    #[Route('/profile/{slug}', name: 'app_user_profile', methods: ['GET'])]
    public function profile(string $slug, UserRepository $userRepository, ImageRepository $imageRepository, LikeRepository $likeRepository): Response
    {
        // Find user by slug or throw 404
        $user = $userRepository->findOneBy(['slug' => $slug]);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Get user's images and posts
        $images = $user->getImages();
        $posts = $user->getPosts();

        // Calculate likes for each image's post
        $likeCounts = [];
        foreach ($images as $image) {
            if ($image->getPost()) {
                $likeCounts[$image->getPost()->getId()] = $likeRepository->count(['post' => $image->getPost()]);
            }
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'images' => $images,
            'likeCounts' => $likeCounts,
        ]);
    }
}
