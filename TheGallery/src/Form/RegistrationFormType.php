<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Regex; 
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', null, [
                'label' => 'Nom d\'utilisateur',
                'attr' => [
                    'placeholder' => 'Entrez votre nom d\'utilisateur',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide',
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 20,
                        'minMessage' => "Votre nom d'utilisateur doit avoir minimum {{ limit }} caractères",
                        'maxMessage' => "Votre nom d'utilisateur doit avoir maximum {{ limit }} caractères",
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_]+$/',
                        'message' => "nom d'utilisateur ne peut contenir que lettres, nombres et underscore.",
                    ])
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [
                    new Email([
                        'message' => 'Entrer une adresse email valide.',
                    ]),
                ],
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Biographie (optionnel)',
                
                'attr' => [
                    'placeholder' => 'Entrez une courte biographie (max 255 caractères)',
                    'maxlength' => 255,
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => "Votre biographie ne peut pas dépasser {{ limit }} caractères",
                    ]),
                ],
                'required' => false
            ])
            ->add('location', null, [
                'label' => 'Localisation (optionnel)',
                'attr' => [
                    'placeholder' => 'Entrez votre localisation (ex: Paris, France)',
                ],
                'required' => false
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'En cochant cette case, vous acceptez que vos données entrées soit utilisés pour la connexion et la sécurité du compte. Vous pouvez à tout moment supprimer votre compte ou demander à le supprimer.',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les termes et conditions.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => 'Mot de passe',
                'required' => true,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9!$%^&*()_+={}:;,.?]*$/',
                        'message' => 'Password can only contain letters, numbers, and specific symbols.',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // assurez-vous que cela correspond à ce qui est attendu
            'csrf_token_id' => 'registration_form',
        ]);
    }
}
