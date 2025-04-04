<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;

class UserTest extends TestCase 
{
    public function testValusernameEmail(){
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail("ahri@testmail.com");

        $validator = Validation::createValidatorBuilder();
        $violations = $validator->getValidator()->validate($user->getEmail(), new Email());

        $this->assertCount(0, $violations, "L'email doit être valide.");
        $this->assertEquals('test_user', $user->getUsername(), "Le username doit être correctement défini.");

        if (count($violations) === 0) {
            echo " Email valide\n";
        } else {
            echo " Email invalide\n";
        }

         // Vérifie que le username est non vide
        if (!empty($user->getUsername())) {
            echo " Username défini : " . $user->getUsername() . "\n";
        } else {
            echo " Username vide ou invalide\n";
        }

        
    }

}