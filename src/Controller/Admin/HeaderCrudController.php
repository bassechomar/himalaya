<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        // condition pour rendre non obligatoire dans le cas d'une modification le champs image
        $required = true;
        if($pageName == 'edit'){
            $required = false;
        } 

        return [
            // IdField::new('id'), // ==> nous n'avons pas besoin du champs id pour ce cas précis
            TextField::new('title', 'Titre'),
            TextareaField::new('content', 'Contenu'),
            TextField::new('buttonTitle', 'Titre du bouton'),
            TextField::new('buttonLink', 'URL du bouton'),
            ImageField::new('image')->setLabel('Image de fond du header')
                                    ->setHelp('Image de fond du header en JPG')
                                    ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]')
                                    ->setBasePath('/uploads')
                                    ->setUploadDir('/public/uploads')
                                    ->setRequired($required)
            // TextEditorField::new('description'),
        ];
    }
    
}
