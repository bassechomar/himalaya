<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Carrier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

// formulaire pour le chox de l'adresse de livraison de l'utilisateur 
class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // dd($options);
        $builder
            // lister tous les adresses de l'utilisateur
            // EntityType permet de preciser a symfony que cette entité existe déjà en base de données
            // Pour se limiter aux adresses de l'utilisateur actif ==> [ array $options]  
            ->add('addresses', EntityType::class, [ 
                'label'=>'Choisissez votre adresse de livraison',
                'required'=> true,                 // obligatoire 
                'class'=> Address::class,          // précise l'entité concernée par ce champ  
                'expanded'=> true,                 // Permet de passer ce champ en cases à cocher 
                'choices'=> $options['addresses'], // array options définie dans la création du formulaire dans le controller
                'label_html'=> true,               // c'est pour pouvoir écrire du html dans la class Address.php dans la méthode __toString()
            ])

            // choix du transporteur // similaire au choix d'adresse 
            ->add('carriers', EntityType::class, [ 
                'label'=>'Choisissez votre transporteur',
                'required'=> true,                  
                'class'=> Carrier::class,          
                'expanded'=> true,                 
                'choices'=> $options['carriers'], 
                'label_html'=> true,               
            ])
            // bouton valider
            ->add('submit', SubmitType::class , [
                'label'=>'Valider',
                'attr'=> [
                    'class'=> 'btn btn-success w-100'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           // Configure your form options here // [ fait refence au 'array $options' ==> donné en paramettre de la methode buildForm ]
          'carriers' => null, 
          'addresses' => null,   // on déclare la clé 'addresses' par defaut à null 
        ]);
    }
}
