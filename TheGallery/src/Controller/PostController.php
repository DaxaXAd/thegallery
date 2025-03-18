<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Image;
use App\Form\PostType;
use App\Form\ImageType;
use App\Repository\PostRepository;
use App\Repository\ImageRepository;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;

#[Route('/post')]
final class PostController extends AbstractController
{
    #[Route(name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, LikeRepository $likeRepository): Response
    {

        $posts = $postRepository->findBy([], ['created_at' => 'DESC']);
        $likeCount = [];
        foreach ($posts as $post) {
            $likeCount[$post->getId()] = $likeRepository->totalLike($post->getId());
        }

        // $commentCounts = [];
        // foreach ($posts as $post) {
        //     $commentCounts[$post->getId()] = $commentRepository->countComment($post->getId());
        // }

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'likeCount' => $likeCount,
        ]);
    }





    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
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
                    $post->setIdImg($image);
                }
            }

            if ($title) {
                $post->setTitle($title);
            }

            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $post->setIdUser($user);
                $post->setCreatedAt(new \DateTimeImmutable());

                $entityManager->persist($post);
                $entityManager->flush();



                return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
            }

            $images = $imageRepository->findBy(['id_user' => $user]);

            return $this->render('post/new.html.twig', [
                'post' => $post,
                'form' => $form->createView(),
                'images' => $images,
            ]);
        } else {
            throw new \Exception('User not found or not authenticated.');
        }
    }






    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post, LikeRepository $likeRepository): Response
    {
        $user = $this->getUser();
        $likeCount = $likeRepository->totalLike($post->getId());

        return $this->render('post/show.html.twig', [
            'likeCount' => $likeCount,
            'user' => $user,
            'post' => $post,
        ]);
    }





    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }




    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            $entityManager->remove($post->getidImg());
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
