<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Tag;
use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
        ->add('addImage', ButtonType::class, [
            'label' => 'Add Image',
            'attr' => [
            // On va récupérer la valeur du champ #post_title en JS et l’ajouter en param
            'onclick' => "
                const titleInput = document.getElementById('post_title');
                const titleVal = titleInput ? encodeURIComponent(titleInput.value) : '';
                window.location.href = '".$options['image_add_url']."?title=' + titleVal;
            "
            ],
            // 'attr' => ['onclick' => 'window.location.href="' . $options['image_add_url'] . '"']
        ])        
        ->add('title', TextType::class, [
            'label' => 'Title',
            'required' => true,
        ])
        ->add('tags', EntityType::class, [
            'class' => Tag::class,
            'choice_label' => 'nameTag',
            'multiple' => true,       // Permet de sélectionner plusieurs tags
            'expanded' => false,      // false => liste déroulante multiple, true => cases à cocher
            'by_reference' => false,  // Important pour ManyToMany
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
