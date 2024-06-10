<?php

namespace App\Controller\Admin;

use App\Entity\User;


use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {   // cette methode sert à définir le class concerné par le crud admin 
        // Fqcn == Full qualified class name 
        return User::class;
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        // 
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setPaginatorPageSize(4)

        ;
    }
    // la methode configureFieds() permet de configurer les diferents champs configurables et visibles pour l'admin 
    // les champs dont les Admin auront besoin et auront le droit de modifier 
    // exmple: Modier le email de User (non) mais auront le droit de modifier le nom, le prenom ou même de supprimer un utilisateur
    // l'adresse email est en plus la clé de connexion 
    public function configureFields(string $pageName): iterable
    {
        return [
            // Rappel de comment gerer les Forms avec le typage des champs bon c'est un peu la même chose 
            // mais à la place de TextType on met TextField 
            // a la place d'envoyer un tableau en 3ième argument comme dans les forms dans le cas des TexFields on applique des methodes (setLabel())
            TextField::new('firstname')->setLabel('Prenom'),
            TextField::new('lastname')->setLabel('Nom'),
            ChoiceField::new('roles')->setLabel('Permissions')->setHelp('Vous pouvez choisir le rôle à attribuer à cet utilisateur ')->setChoices([
                'ROLE_USER'=> 'USER_ROLE',
                'ROLE_ADMIN'=> 'ROLE_ADMIN',
            ])->allowMultipleChoices(), // pour indiquer a symfony que plusieurs choix sont possible 
            // Rendre un champs visible par l'Admin mais non modifiable avec la methode onlyOnIndex()
            TextField::new('email')->setLabel('Email')->onlyOnIndex(), 
            
            // IdField::new('id'),
            // TextField::new('title'),
            // TextEditorField::new('description'),
        ];
    }
    
}
