<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class AddressUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname',TextType::class, [
                'label'=> 'Votre prénom',
                'attr'=> [
                    'placeholder'=>'Indiquer votre nom', 
                ]
            ])
            ->add('lastname', TextType::class, [
                'label'=>'Votre nom',
                'attr'=> [
                    'placeholder'=> 'Votre nom', 
                ]
            ])
            ->add('address' , TextType::class, [
                'label'=> 'Votre adresse',
                'attr'=>[
                    'placeholder'=> '45 rue du Marchand '
                ]
            ])
            ->add('postal', TextType::class, [
                'label'=> 'CP ',
                'attr'=>[
                    'placeholder'=>' Votre code postal '
                ]
            ])
          
            ->add('city', TextType::class, [
                'label'=> 'Ville ',
                'attr'=>[
                    'placeholder'=>' Le nom de votre ville '
                ]
            ])
         
            ->add('country', CountryType::class, [
                'label'=> 'Votre pays ',
                'attr'=>[
                    'placeholder'=>'Pays de residence'
                ]
            ])
            ->add('phone', TextType::class, [ // pour avoir le prifixe +33.... on choisit TextType::class plutôt que NumberType 
                'label'=> 'Votre numéro de téléphone ',
                'attr'=>[
                    'placeholder'=>' Indiquer votre numero de téléphone '
                ]
            ])
            // Soumission du formulaire 
            ->add('submit', SubmitType::class, [
                'label'=>'Sauvegarder',
                'attr'=>[
                    'class'=>'btn btn-success w-100'
                ]
            ]);
            // dans le tuto cette partie génère une erreur // mise en commentaire 
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
