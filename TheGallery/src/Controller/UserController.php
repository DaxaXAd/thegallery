<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Post;
use App\Entity\Image;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\PostsRepository;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Controller\SecurityController;
use App\Repository\LikeRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

#[Route('/user')]
final class UserController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/user/{slug}', name: 'app_user_index', methods: ['GET'])]
    public function index(int $id, UserRepository $userRepository, LikeRepository $likeRepository, string $slug): Response
    {
        $user = $userRepository->findOneBy(['slug' => $slug]);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $posts = $user->getPosts();

        $likeCount = [];
        foreach ($posts as $post) {
            // Appel à ta méthode custom (ou la méthode native count(['post' => $post]))
            $likeCount[$post->getId()] = $likeRepository->totalLike($post->getId());
        }

        $likeCount = $likeRepository->totalLike($user->getPosts());

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'likeCount' => $likeCount,
        ]);
    }




    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles(['ROLE_USER']);

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




    #[Route('/{slug}', name: 'app_user_show', methods: ['GET'])]
    public function show(UserRepository $userRepository, LikeRepository $likeRepository, string $slug): Response
    {
        $user = $userRepository->findOneBy(['slug' => $slug]);
        // $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $posts = $user->getPosts();

        $likeCount = [];
        foreach ($posts as $post) {
            // Appel à ta méthode custom (ou la méthode native count(['post' => $post]))
            $likeCount[$post->getId()] = $likeRepository->totalLike($post->getId());
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'likeCount' => $likeCount,
        ]);
    }



    #[Route('/{slug}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, string $slug): Response
    {
        $user = $userRepository->findOneBy(['slug' => $slug]);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        


        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Hashing and manage password
            $newPassword = $form->get('password')->getData();
            if (!empty($newPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            // manage and add profile_pic
            $profilePicture = $form->get('profile_pic')->getData();
            if ($profilePicture) {

                $pictureFilename = uniqid() . '.' . $profilePicture->guessExtension();
                try {
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




    #[Route('/{slug}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, string $slug, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneBy(['slug' => $slug]);
        if ($this->isCsrfTokenValid('delete' . $user->getSlug(), $request->getPayload()->getString('_token'))) {


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

        return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
    }




    #[Route('/profile/{slug}', name: 'app_user_profile', methods: ['GET'])]
    public function profile(string $slug, UserRepository $userRepository, ImageRepository $imageRepository, ManagerRegistry $doctrine): Response
    {
        $user = $userRepository->findOneBy(['slug' => $slug]);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Récupération des images via la relation OneToMany
        $images = $user->getImages();

        // Récupération des posts via la relation OneToMany
        $posts = $user->getPosts();

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'images' => $images,
        ]);
    }
}
