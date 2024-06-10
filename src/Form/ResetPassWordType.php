<?php

namespace App\Form;

use App\Entity\User; // on transporte les 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType as TypeSubmitType;

class ResetPassWordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // ->add('actualPassword', PasswordType::class, [
        //     'label'=>"Votre mot de passe actuel",
        //     'attr'=>[
        //         'placeholder'=>'Indiquer votre mot de passe actuel'
        //     ],
        //     'mapped'=> false
            
        // ])
        ->add('plainPassword', RepeatedType::class,[
            'type' => PasswordType::class,
            'constraints'=> [
                 new Length([
                    'min'=> 4,
                    'max'=> 30
                 ])
            ],
            'first_options'  => [
                'label' => 'Votre nouveau mot de passe', 
                'attr'=> [
                    'placeholder'=> 'Choisissez votre nouveau mot de passe'
                ],
                'hash_property_path' => 'password', // permet de hasher le mot de passe en Bdd et fait lee lien avec dans ORM 
            ],
            'second_options' => [
                'label' => 'Confirmer votre nouveau mot de passe',
                'attr' => [
                    'placeholder'=>'Confirmer votre nouveau mot de passe'
                ]
            ],
            'mapped' => false,
        ])
        ->add('submit', TypeSubmitType::class, [
            'label'=> 'Mettre à jour mon mot de passe', // le nom du champs 
            'attr'=>[                                   // les attributs du champ 
                'class'=>'btn btn-success w-100'        // changement de la couleur par défaut 
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, 
            // Configure your form options here
        ]);
    }
}
