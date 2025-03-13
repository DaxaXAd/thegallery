<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Post;
use App\Form\LikeType;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/like')]
final class LikeController extends AbstractController
{
    #[Route('/{id}',name: 'app_like')]
    public function addLike(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user){ 
            // Redirige vers l'inscription ou la connexion si l'utilisateur n'est pas authentifié
            return $this->redirectToRoute('app_login');
        }
        // Vérifier si le like existe déjà pour ce post par cet utilisateur
        $checkLike = $entityManager->getRepository(Like::class)->findOneBy([
            'post' => $post,
            'user' => $user,
        ]);
    
        if ($checkLike) {
            // Si le like existe, on le supprime (annulation du like)
            $entityManager->remove($checkLike);
            $entityManager->flush();
            $this->addFlash('info', 'Unliked.');
        } else {
            // Sinon, on crée un nouveau like
            $like = new Like();
            $like -> setPost($post);
            $like -> setUser($user);
    
            $entityManager->persist($like);
            $entityManager->flush();
    
            $this->addFlash('success', 'Liked !');
        }
        // Rediriger vers l'index des posts (ou vers la page du post)
        return $this->redirectToRoute('app_post_index');
    }

    #[Route(name:'show_like')]
    public function showNumberLike($postId, EntityManagerInterface $entityManager): Response
    {
        $numberLike = $entityManager->getRepository(Like::class)->count(['post' => $postId,]);
        return $this->json(['likes' => $numberLike]);
    }

}


// namespace App\Controller;

// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Attribute\Route;

// final class LikeController extends AbstractController
// {
//     #[Route('/like', name: 'app_like')]
//     public function index(): Response
//     {
//         return $this->render('like/index.html.twig', [
//             'controller_name' => 'LikeController',
//         ]);
//     }
// }
