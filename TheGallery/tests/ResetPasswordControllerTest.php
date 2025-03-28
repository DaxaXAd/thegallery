<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ResetPasswordControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private MailerInterface $mailer;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        // Récupérer l'EntityManager et le Mailer
        $container = static::getContainer();
        $this->em = $container->get('doctrine')->getManager();
        $this->mailer = $container->get(MailerInterface::class); // Utilisation de MailerInterface ici

        $this->userRepository = $container->get(UserRepository::class);

        foreach ($this->userRepository->findAll() as $user) {
            $this->em->remove($user);
        }

        $this->em->flush();
    }

    public function testEmailSent(): void
    {
        // Création d'un utilisateur pour tester l'envoi du mail
        $user = (new User())
            ->setEmail('l.hami@orange.fr')
            ->setUsername('AhriTest')
            ->setPassword('password123')
        ;

        // Persister l'utilisateur pour tester l'email
        $this->em->persist($user);
        $this->em->flush();

        // Récupérer le client
        $client = $this->client;
        $client->request('GET', '/reset-password');  // URL de demande de reset

        // Soumettre le formulaire pour demander le réinitialisation du mot de passe
        $client->submitForm('Send password reset email', [
            'reset_password_request[email]' => 'l.hami@orange.fr',
        ]);

        // Vérifier que l'email a bien été envoyé
        self::assertEmailCount(1); // Vérifie que 1 email a été envoyé

        // Récupérer l'email envoyé avec Symfony Mailer
        $messages = $this->getMailerMessages();  // Récupère les messages envoyés

        // Vérifier que l'email contient l'adresse correcte et le message attendu
        self::assertEmailAddressContains($messages[0], 'to', 'l.hami@orange.fr');
        self::assertEmailTextBodyContains($messages[0], 'Please follow the link to reset your password');
    }

    protected function tearDown(): void
    {
        // Supprimer l'utilisateur après test
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'l.hami@orange.fr']);
        if ($user) {
            $this->em->remove($user);
            $this->em->flush();
        }
    }
}



// namespace App\Tests;

// use App\Entity\User;
// use App\Repository\UserRepository;
// use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Bundle\FrameworkBundle\KernelBrowser;
// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
// use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// class ResetPasswordControllerTest extends WebTestCase
// {
//     private KernelBrowser $client;
//     private EntityManagerInterface $em;
//     private UserRepository $userRepository;

//     protected function setUp(): void
//     {
//         $this->client = static::createClient();

//         // Ensure we have a clean database
//         $container = static::getContainer();

//         /** @var EntityManagerInterface $em */
//         $em = $container->get('doctrine')->getManager();
//         $this->em = $em;

//         $this->userRepository = $container->get(UserRepository::class);

//         foreach ($this->userRepository->findAll() as $user) {
//             $this->em->remove($user);
//         }

//         $this->em->flush();
//     }

//     public function testResetPasswordController(): void
//     {
//         // Create a test user
//         $user = (new User())
//             ->setEmail('l.hami@orange.fr')
//             ->setUsername('AhriTest')
//             ->setProfilePic('default.png')
//             ->setPassword('a-test-password-that-will-be-changed-later')
//             ->setSlug('ahritest')
//         ;
//         $this->em->persist($user);
//         $this->em->flush();

//         // Vérification si le slug est bien généré
//         self::assertNotNull($user->getSlug());

//         // Test Request reset password page
//         $this->client->request('GET', '/reset-password');

//         self::assertResponseIsSuccessful();
//         self::assertPageTitleContains('Reset your password');

//         // Submit the reset password form and test email message is queued / sent
//         $this->client->submitForm('Send password reset email', [
//             'reset_password_request[email]' => 'l.hami@orange.fr',
//         ]);

//         // Ensure the reset password email was sent
//         // Use either assertQueuedEmailCount() || assertEmailCount() depending on your mailer setup
//         // self::assertQueuedEmailCount(1);
//         self::assertEmailCount(1);

//         self::assertCount(1, $messages = $this->getMailerMessages());

//         self::assertEmailAddressContains($messages[0], 'from', 'admin@thegallery.com');
//         self::assertEmailAddressContains($messages[0], 'to', 'l.hami@orange.fr');
//         self::assertEmailTextBodyContains($messages[0], 'This link will expire in 1 hour.');

//         self::assertResponseRedirects('/reset-password/check-email');

//         // Test check email landing page shows correct "expires at" time
//         $crawler = $this->client->followRedirect();

//         self::assertPageTitleContains('Password Reset Email Sent');
//         self::assertStringContainsString('This link will expire in 1 hour', $crawler->html());

//         // Test the link sent in the email is valid
//         $email = $messages[0]->toString();
//         preg_match('#(/reset-password/reset/[a-zA-Z0-9]+)#', $email, $resetLink);

//         $this->client->request('GET', $resetLink[1]);

//         self::assertResponseRedirects('/reset-password/reset');

//         $this->client->followRedirect();

//         // Test we can set a new password
//         $this->client->submitForm('Reset password', [
//             'change_password_form[plainPassword][first]' => 'newStrongPassword',
//             'change_password_form[plainPassword][second]' => 'newStrongPassword',
//         ]);

//         self::assertResponseRedirects('/login');

//         $user = $this->userRepository->findOneBy(['email' => 'l.hami@orange.fr']);

//         self::assertInstanceOf(User::class, $user);

//         /** @var UserPasswordHasherInterface $passwordHasher */
//         $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);
//         self::assertTrue($passwordHasher->isPasswordValid($user, 'newStrongPassword'));

//         $resetRequest = $this->em->getRepository(\App\Entity\ResetPasswordRequest::class)->findOneBy([
//             'user' => $user,
//         ]);

//         if ($resetRequest) {
//             $this->em->remove($resetRequest);
//         }
//         $this->em->remove($user);
//         $this->em->flush();
//     }
// }