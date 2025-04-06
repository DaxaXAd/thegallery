<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class TestMailController extends AbstractController
{
    #[Route('/test/mail', name: 'app_test_mail')]
    // public function index(): Response
    // {
    //     return $this->render('test_mail/index.html.twig', [
    //         'controller_name' => 'TestMailController',
    //     ]);
    // }

    public function index(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('no-reply@mailtrap.io')
            ->to('l.hami@orange.fr') // remplace par ton adresse Mailtrap !
            ->subject('📧 Test d\'envoi via Mailtrap (index)')
            ->text('Ceci est un email envoyé depuis la méthode index() du contrôleur généré.');

        try {
            $mailer->send($email);
        } catch (\Exception $e) {
            return new Response('Erreur : ' . $e->getMessage());
        }

        return new Response('Email envoyé avec succès depuis index() ✨');
    }
}
