<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        // 
            ->setEntityLabelInSingular('Produit') // Traduction en langue française 
            ->setEntityLabelInPlural('Produits')
            ->setPaginatorPageSize(8)
        ;
    }

   
    public function configureFields(string $pageName): iterable
    {    
        // reglage du pb edit image // non obligatoire en modification  
        // dd($pageName);  // vaut edit en modification // vaut new en modification 
        $required = true; 

        if($pageName == 'edit'){
            $required = false; // $required est la variable donnée en parametre  à la méthode setRequired() du champs ImageField 
        }

        return [
            TextField::new('name')
                        ->setLabel('Nom'),
            BooleanField::new('isHomePage')
                          ->setLabel('Produit à la une')
                          ->setHelp(" Vous permet d'afficher un produit sur la home page d'acceuil"),
            SlugField::new('slug')
                        ->setLabel('URL')
                        ->setTargetFieldName('name') 
                        ->setHelp('l\'url visible dans la barre de navigation '),
            // TextareaField::new('descrption'): une possibilité non retenue dans le cas qi nous concerne 
            // EasyAdmin embarque un éditeur de text (wisigy) un éditeur de mise en forme de texte 
            TextEditorField::new('description')
                        ->setLabel('Description')
                        ->setHelp('Descrition de votre produit'),
            // ajouter un input-file  
            // Pour lié une image à un produit, EasyAdmin nous demande d'indiquer le repertoire ou se trouve l'image 
            ImageField::new('image')
                        ->setLabel('Image')
                        ->setHelp('Image de produit en 600x600px')
                        ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]') // pour encoder le nom du fichier image
                        ->setBasePath('/uploads')
                        ->setUploadDir('/public/uploads')
                        ->setRequired($required),
            NumberField::new('price')
                        ->setLabel('Prix H.T')
                        ->setHelp('Prix H.T du produit sans le sigle €'),
            //setHelp('Choisir dans liste le taux de TVA à appliquer pour ce produit '),
            
            ChoiceField::new('tva')
                        ->setLabel('Taux TVA')
                        ->setChoices([
                            //partie de gauche ce qu'on affiche à l'utilisateur(l'Admin)
                            //partie droite est ce qu'on stockera en base de données 
                            '5.5%'=>'5.5',
                            '10%'=> '10',
                            '20%'=> '20'
            ]),
            // affichage des categories dans le formaulaire edit produit
            // choix de la catégorie dont appartiendra le produit en cours de création(édition)
            AssociationField::new('category')->setLabel('Catégorie à associer au produit'),
      
        ];
    }
  
}
