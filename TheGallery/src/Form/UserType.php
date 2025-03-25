<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{

    private $security;

    public function __construct(SecurityBundleSecurity $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'votre adresse mail',
                'required' => true,
            ])
            // ->add('roles')
            ->add('password', PasswordType::class, [
                'required' => false, 
                // 'empty_data' => '',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Keep it empty if you wish to keep your actual password.',  
                ],
                'constraints' => [
                new Length([
                    'min' => 8,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),],
            ])
            ->add('username')
            ->add('bio')
            ->add('profile_pic', FileType::class, [
                'label' => 'Photo de profil (JPG, PNG)', 
                'mapped' => false,
                'required' => false, 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            // 'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // ✅ Tu peux spécifier un token ID personnalisé, mais ce n’est pas obligatoire :
            'csrf_token_id'   => 'edit_user', // utilisé uniquement côté serveur pour vérifier que c’est bien ce formulaire
        ]);
    }
}
