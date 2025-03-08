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

#[Route('/likes')]
final class LikeController extends AbstractController
{
    #[Route('/add/{id}',name: 'app_like')]
    public function addLike(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user){
            return $this->redirectToRoute('app_login');
        }

        $existingLike = $entityManager->getRepository(Like::class)->findOneBy([
            'post' => $post,
            'user' => $user,
        ]);
    
        if ($existingLike) {
            $entityManager->remove($existingLike);
            $entityManager->flush();
            $this->addFlash('info', 'Vous avez annulé votre Like.');
        }

        else {
            $like = new Like();
            $like -> setPosts($post);
            $like -> setUsers($user);
    
            $entityManager->persist($like);
            $entityManager->flush();
    
            $this->addFlash('success', 'Vous avez like ce message !');
        }

        return $this->redirectToRoute('app_posts_index');
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
