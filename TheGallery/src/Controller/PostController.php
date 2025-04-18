<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Comment;
use App\Form\PostType;
use App\Form\ImageType;
use App\Form\CommentType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\PostRepository;
use App\Repository\ImageRepository;
use App\Repository\LikeRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;

#[Route('/post')]
#[IsGranted('ROLE_USER')]
final class PostController extends AbstractController
{
    #[Route(name: 'app_post_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(PostRepository $postRepository, LikeRepository $likeRepository, TagRepository $tagRepository): Response
    {

        $posts = $postRepository->findBy([], ['created_at' => 'DESC']);
        $tags = $tagRepository->findAll(); // ajout
        $likeCount = [];
        foreach ($posts as $post) {
            $likeCount[$post->getId()] = $likeRepository->totalLike($post->getId());
        }

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'tags' => $tags, // ajout tags
            'likeCount' => $likeCount,
        ]);
    }





    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager, ImageRepository $imageRepository, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        if ($user) {
            $image = new Image();
            $post = new Post();
            $imageId = $request->query->get('imageId');
            $title = $request->query->get('title');

            if ($imageId) {
                $image = $imageRepository->find($imageId);
                if ($image) {
                    $post->setimg($image);
                }
            }

            if ($title) {
                $post->setTitle($title);
            }

            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $post->setuser($user);
                $selectedImageId = $request->request->get('selectedImageId');
                if ($selectedImageId) {
                    $image = $imageRepository->find($selectedImageId);
                    if ($image) {
                        $post->setimg($image);
                    }
                }
                
                $post->setCreatedAt(new \DateTimeImmutable());

                $entityManager->persist($post);
                $entityManager->flush();



                return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
            }

            $images = $imageRepository->findBy(['user' => $user]);

            return $this->render('post/new.html.twig', [
                'post' => $post,
                'form' => $form->createView(),
                'images' => $images,
            ]);
        } else {
            throw new \Exception('User not found or not authenticated.');
        }
    }






    #[Route('/{id}', name: 'app_post_show', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function show(Request $request, Post $post, LikeRepository $likeRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $likeCount = $likeRepository->totalLike($post->getId());

        // 1) Instancier un nouvel objet Comment
        $comment = new Comment();

        // 2) On pré-remplit le post et l'user
        $comment->setPost($post);
        $comment->setuser($this->getUser()); // si l'utilisateur est connecté
        $comment->setCreatedAt(new \DateTimeImmutable());

        // 3) Créer le formulaire
        $form = $this->createForm(CommentType::class, $comment);

        // 4) Gérer la soumission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder le commentaire
            $entityManager->persist($comment);
            $entityManager->flush();

            // Rediriger pour éviter la resoumission
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        // Récupérer la liste des commentaires
        // (ou tu peux directement faire $post->getIdComment() selon ton entité)
        $comments = $post->getIdComment(); // c'est un Collection


        return $this->render('post/show.html.twig', [
            'likeCount' => $likeCount,
            'user' => $user,
            'post' => $post,
            'comments' => $comments,
            'form' => $form->createView(),
        ]);
    }





    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager, ImageRepository $imageRepository): Response
    {
        
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedImageId = $request->request->get('selectedImageId');
            if ($selectedImageId) {
                $image = $imageRepository->find($selectedImageId);
                if ($image) {
                    $post->setimg($image);
                }
            }
            $post->setCreatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }
        $images = $imageRepository->findBy(['user' => $this->getUser()]);

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
            'images' => $images,
        ]);
    }




    #[Route('/{id}/delete', name: 'app_post_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            // $entityManager->remove($post->getimg());
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
