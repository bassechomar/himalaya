<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }
   
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        // 
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
            ->setPaginatorPageSize(4)
        ;
    }
    public function configureActions(Actions $actions): Actions
    {  // Easyadmin nous permet de créer nos propres actions comme sur la ligne suivante
        $show = Action::new('Afficher')->linkToCrudAction('show');
    // configuration de la page commande du Dashboard pour limiter les actions(les droits, ou possibilités) de l'admin
    // Dans ce qui suit l'admin n'aura pas la possibilité de supprimer /editer / et créer une commande
    // nous ajouterons ensuite des boutons personnalisés / ex: consulter une commande ou voir la commande 
    return $actions
        // on rajoute l'action
         ->add(Crud::PAGE_INDEX, $show)    // Pour cosulter les details d'une commande 
        // ->add(Crud::PAGE_INDEX, Action::DETAIL)  
        // on supprime les actions que nous les possibilités de l'administrateur de faire 
        ->remove(Crud::PAGE_INDEX, Action::NEW )   // indiquer a quel endroit on souhaite supprimer une Action en premiere parametre 
        ->remove(Crud::PAGE_INDEX, Action::DELETE) // et en deuxieme parametre lui indiquer quelle Action on souhaite supprimer 
        ->remove(Crud::PAGE_INDEX, Action::EDIT);  // ex: sur cette ligne l'Admin n'aura plus la possibilité de d'éditer une commande 
    }
    // création de la methode show() pour configurer la vue actions (->add(Crud::PAGE_INDEX, $show))
    public function show(AdminContext $context){ // Easyadmin se balade avec AdminContext et dans EasyAdmin il incorpore a chaque fois l'instance de l'entité active
       // AdminContext garde en mémoire l'entité active que nous pouvons récupérer avec les getter d'entité et le getter d'instnance de l'entité en cours 
       $order = $context->getEntity()->getInstance();
    //    dd($order); // test pour voir notre objet 

       return $this->render('admin/order.html.twig', [ // on crée un fichier template 'admin/order.html.twig pour affichage de la vue admin/commande 
         'order'=> $order, 
       ]); // 
    }

    // ici on personnalise les champs (entrées) du tableau commandes  
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateField::new('createdAt')->setLabel('Date'), // date de creation 
            NumberField::new('state')->setLabel('Statut')->setTemplatePath('admin/state.html.twig'), // le statut de la commande 
            AssociationField::new('user')->setLabel('Utilisateur'), // pour changer (User#10) se rendre dans l'entité User et créer une methode __toString()  // créer une relation en cmd entre un Order et User || un User peut avoir plusieurs Order 
            TextField::new('carrierName')->setLabel('Transporteur'),
            NumberField::new('totalTva')->setLabel('Total TVA'),
            NumberField::new('totalWt')->setLabel('Total T.T.C'),
   
        ];
    }
    
}
