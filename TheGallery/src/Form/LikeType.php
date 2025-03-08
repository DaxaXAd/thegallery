<?php

namespace App\Form;

use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LikeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('post_id', EntityType::class, [
                'class' => Post::class,
                'choice_label' => 'id',
            ])
            ->add('user_id', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ]);
            // ->add('users', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'user_id',
            // ])
            // ->add('posts', EntityType::class, [
            //     'class' => Post::class,
            //     'choice_label' => 'user_id',
            // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Like::class,
        ]);
    }
}
