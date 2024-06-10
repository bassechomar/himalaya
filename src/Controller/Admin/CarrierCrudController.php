<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CarrierCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Carrier::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInSingular('Carrier')
                    ->setEntityLabelInPlural('Carrier')
                    ->setPaginatorPageSize(4)
          ;
    }
    
    public function configureFields(string $pageName): iterable
    {   // configauation des champs 
        return [
            IdField::new('name')->setLabel('Nom du transporteur'),
            // TextField::new('title'),
            TextareaField::new('description')->setLabel('Description du transporteur'),
            NumberField::new('price')->setLabel('Prix H.T')->setHelp('Prix H.T du transporteur sans le sigle €'),
            //setHelp('Choisir dans le choices types le taux de TVA à appliquer pour le produit en cours de création'),

        ];
    }
    
}
