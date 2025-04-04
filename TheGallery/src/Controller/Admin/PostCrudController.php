<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
    
    public function configureFields(string $pageName): iterable
    {
        return [
             // On cache l'id en formulaire
            IdField::new('id')->hideOnForm(),

             // Titre du post
            TextField::new('title', 'Titre'),

             // Date de création
            DateTimeField::new('created_at', 'Date de création')->hideOnForm(),// Par exemple, si tu la gères en code

             // Relation vers l’utilisateur
            AssociationField::new('user', 'Utilisateur'),

             // Relation vers l'image
            AssociationField::new('img', 'Image')
                 ->hideOnIndex(), // Optionnel
        ];
    }
    
}
