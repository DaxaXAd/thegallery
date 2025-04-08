<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'title',
                'required' => true,
            ])
            ->add('path', FileType::class, [
                'label' => 'image',
                'mapped' => false, // This field is not mapped to the entity property
                'attr' => [
                    'accept' => 'image/jpeg, image/png, image/webp',
                ],
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '8M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Formats autorisés : JPEG, PNG, WEBP.',
                    ])
                ]
            ])
            ->add('id_tag', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'nameTag', // Affiche la propriété "nameTag" de l'entité Tag
                'label' => 'Tag',
                'placeholder' => 'Sélectionnez un tag',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
