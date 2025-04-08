<?php


namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends TestCase
{
    /**
     * Test simple des getters/setters pour l'email.
     * ⚠️ Ce test ne vérifie pas la validité de l'email, seulement le getter/setter.
     */
    public function testEmailSetterGetter(): void
    {
        $user = new User();
        $email = 'ahri@foxmail.com';
        $user->setEmail($email);

        // On teste juste que le setter et getter fonctionnent bien
        $this->assertSame($email, $user->getEmail(), 'L\'email retourné ne correspond pas à celui défini.');
    }

    /**
     * Test d'un email invalide avec le Validator de Symfony.
     */
    public function testEmailIsInvalid(): void
    {
        $emailInvalide = 'ahri@foxmail'; // Pas de TLD
        $validator = Validation::createValidator();
        $violations = $validator->validate($emailInvalide, new Email());

        // On s'attend à au moins une violation
        $this->assertGreaterThan(
            0,
            count($violations),
            'Un email invalide doit générer une violation de contrainte.'
        );
    }

    /**
     * Test d'un email valide avec le Validator de Symfony.
     */
    public function testEmailIsValid(): void
    {
        $emailValide = 'ahri@foxmail.com';
        $validator = Validation::createValidator();
        $violations = $validator->validate($emailValide, new Email());

        // Aucun message d'erreur attendu ici
        $this->assertCount(
            0,
            $violations,
            'Un email valide ne doit générer aucune violation.'
        );
    }

    /**
     * Test simple du getter/setter du nom d'utilisateur.
     */
    public function testUsername(): void
    {
        $user = new User();
        $username = 'ahri';
        $user->setUsername($username);

        $this->assertSame($username, $user->getUsername(), 'Le nom d\'utilisateur doit être identique.');
    }

    /**
     * Test d'un nom d'utilisateur valide avec la méthode métier isUsernameValid().
     */
    public function testUsernameIsValid(): void
    {
        $user = new User();
        $user->setUsername('ahri'); // 4 caractères, valide

        $this->assertTrue($user->isUsernameValid(), 'Le nom d\'utilisateur devrait être valide.');
    }

    /**
     * Test d'un nom d'utilisateur invalide avec la méthode métier.
     */
    public function testUsernameIsInvalid(): void
    {
        $user = new User();
        $user->setUsername('a'); // Trop court

        $this->assertFalse($user->isUsernameValid(), 'Un nom trop court ne doit pas être considéré comme valide.');
    }

    /**
     * Test simple du getter/setter du mot de passe.
     * ⚠️ Attention, ici on ne teste pas le hachage du mot de passe.
     */
    public function testPassword(): void
    {
        $user = new User();
        $password = '12345678';
        $user->setPassword($password);

        $this->assertSame($password, $user->getPassword(), 'Le mot de passe doit être identique à celui défini.');
    }

    /**
     * Test global de l'entité User avec le Validator Symfony.
     * ⚠️ Nécessite que les annotations ou attributes soient activés.
     */
    public function testUserEntityIsValid(): void
    {
        $user = new User();
        $user->setEmail('ahri@foxmail.com');
        $user->setUsername('ahri');
        $user->setPassword('12345678');
        $user->setProfilePic('default.jpg'); // champ requis dans l'entité

        // Création d'un Validator avec mapping des contraintes de l'entité
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping() // active la lecture des attributes (PHP 8+)
            ->getValidator();

        $violations = $validator->validate($user);

        $this->assertCount(0, $violations, 'L\'entité User doit être valide avec ces données.');
    }
}


// namespace App\Tests\Entity;

// use App\Entity\User;
// use PHPUnit\Framework\TestCase;
// use Symfony\Component\Validator\Validation;
// use Symfony\Component\Validator\Constraints\Email;

// class UserTest extends TestCase 
// {
//     public function testEmail(){
//         $user = new User();
//         $email = 'ahri@foxmail.com';
//         $email2 = 'ahri@foxmail';
//         $user->setEmail($email);

//         $this->assertSame($email, $user->getEmail(), 'Email doit être au format d\'email.' );
        
//     }
//     public function testEmailIsValid(){
//         $user = new User();
//         $email = 'ahri@foxmail.com';
//         $email2 = 'ahri@foxmail';
//         $user->setEmail($email);

        
//         $validator = Validation::createValidator();
//         $violations = $validator->validate($email, new Email());
//         $this->assertCount(0, $violations, 'Email doit être au format d\'email.' );
        
//     }


//     public function testUsername(){
//         $user = new User();
//         $username = 'ahri';
//         $user->setUsername($username);

//         $this->assertSame($username, $user->getUsername(), 'Le nom d\'utilisateur doit être le même.' );
//     }
//     public function testUsernameIsValid(){
//         $user = new User();
//         $user->setUsername('ah');
        
//         $this->assertTrue($user->isUsernameValid(), 'Le nom d\'utilisateur doit être valide.' );
//     }

//     public function testPassword(){
//         $user = new User();
//         $password = '12345678';
//         $user->setPassword($password);

//         $this->assertSame($password, $user->getPassword(), 'Le mot de passe doit être le même.' );
//     }
// }