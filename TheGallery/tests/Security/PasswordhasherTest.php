<?php

namespace App\Tests\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class PasswordHasherTest extends TestCase
{
    public function testAutoHasherHashesAndValidatesPassword()
    {
        $hasher = new NativePasswordHasher(); // fonctionne avec bcrypt/argon
        $plainPassword = 'motDePasse123';

        $hashed = $hasher->hash($plainPassword);

        // Test : mot de passe haché ≠ mot de passe brut
        $this->assertNotEquals($plainPassword, $hashed);

        // Test : le mot de passe brut est bien reconnu par le hasher
        $this->assertTrue($hasher->verify($hashed, $plainPassword));
    }

    public function testPasswordHashingWithMockedUser()
    {
        $plainPassword = 'motDePasse456';

        // Mock d'un utilisateur qui implémente PasswordAuthenticatedUserInterface
        $user = $this->createMock(PasswordAuthenticatedUserInterface::class);

        // Utilisation du hasher Symfony "auto"
        $hasher = new NativePasswordHasher();

        // Hashage du mot de passe
        $hashedPassword = $hasher->hash($plainPassword);

        // Le mot de passe haché ne doit pas être égal au mot en clair
        $this->assertNotEquals($plainPassword, $hashedPassword);

        // Le mot de passe est vérifiable
        $this->assertTrue($hasher->verify($hashedPassword, $plainPassword));
    }

    // Test : le mot de passe incorrect ne doit pas être validé
    public function testPasswordFailsWithWrongPassword()
    {
        $plainPassword = 'motDePasse123';
        $wrongPassword = 'mauvaisMotDePasse';

        // Mock d'un utilisateur qui implémente PasswordAuthenticatedUserInterface
        $user = $this->createMock(PasswordAuthenticatedUserInterface::class);

        // Utilisation du hasher Symfony "auto"
        $hasher = new NativePasswordHasher();

        // Hashage du mot de passe
        $hashedPassword = $hasher->hash($plainPassword);

        // Vérification : le mot de passe incorrect ne doit pas être reconnu
        // $this->assertFalse($hasher->verify($hashedPassword, $wrongPassword));

        if($this->assertTrue($hasher->verify($hashedPassword, $wrongPassword))) {
            throw new \Exception("Erreur critique !");
        }
        
    }

    // Test : la différence de casse dans le mot de passe ne doit pas être validée
    public function testPasswordFailsWithCaseDifference()
    {
        $plainPassword = 'motDePasse123';

        // Mock d'un utilisateur qui implémente PasswordAuthenticatedUserInterface
        $user = $this->createMock(PasswordAuthenticatedUserInterface::class);

        // Utilisation du hasher Symfony "auto"
        $hasher = new NativePasswordHasher();

        // Hashage du mot de passe
        $hashedPassword = $hasher->hash($plainPassword);

        // Vérification : un mot de passe avec une casse différente ne doit pas être reconnu
        $this->assertTrue($hasher->verify($hashedPassword, strtoupper($plainPassword)));
    }
}

// 