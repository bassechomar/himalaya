<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }
    public function configureCrud(Crud $crud): Crud
    {  // onfiguration du crud pour rendre traduire les labels en Français 
        return $crud ->setEntityLabelInSingular('Categorie')
                     ->setEntityLabelInPlural('Categories')
                     ->setPaginatorPageSize(4)
            ;
    }

    public function configureFields(string $pageName): iterable
    {  // configuation des champs 
        return [
            TextField::new('name')->setLabel('Titre')->setHelp('Titre de la catégorie'),
            //le slug est differnt /
            SlugField::new('slug')->setLabel('URL')->setTargetFieldName('name')->setHelp('URL de votre votre catégory généré automatiquent'),
            // TextEditorField::new('description'),
        ];
    }
    
}
