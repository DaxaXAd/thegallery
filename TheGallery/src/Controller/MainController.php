<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        if($this->getUser()) {
            return $this->redirectToRoute('app_post_index');
        }
        return $this->render('home.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
