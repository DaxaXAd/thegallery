<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title', TextType::class, [
            'label' => 'Title',
            'required' => true,
        ])
        ->add('photo', FileType::class, [
            'label' => 'Photo',
            'mapped' => false,
            'required' => false,
        ])
        ->add('addImage', ButtonType::class, [
            'label' => 'Add Image',
            'attr' => ['onclick' => 'window.location.href="' . $options['image_add_url'] . '"']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'image_add_url' => '/image/new', // URL pour ajouter une image
        ]);
    }
}
