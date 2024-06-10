<?php

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\Extension\Core\Type\SubmitType as TypeSubmitType;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label'=>'Votre adresse email',
                'attr'=> [
                    'placeholder'=>'Indiquer votre adresse email'
                ]  
            ])
            // // ->add('roles') // on eleve role car le user n'aura pas besoin de le sisir 
            // ->add('password', PasswordType::class, [
            //     'label'=>  'Votre mot de passe',
            //     'attr'=> [
            //         'placeholder'=>'Choisissez votre mot de passe '
            //     ]
            // ])
            // utilisation d'une hash_property_path : pour pemrmettre le transit crypté du mot de passe  
            ->add('plainPassword', RepeatedType::class,[
                'type' => PasswordType::class,
                'constraints'=> [
                     new Length([
                        'min'=> 4,
                        'max'=> 30
                     ])
                ],
                'first_options'  => [
                    'label' => 'Votre mot de passe', 
                    'attr'=> [
                        'placeholder'=> 'Choisissez votre mot de passe'
                    ],
                    'hash_property_path' => 'password', // permet de hasher le mot de passe en Bdd 
                ],
                'second_options' => [
                    'label' => 'Confirmer votre mot de passe',
                    'attr' => [
                        'placeholder'=>'Confirmer votre mot de passe'
                    ]
                ],
                'mapped' => false,
            ])
            ->add('firstname', TextType::class, [
                'label'=> 'Indiquez votre prénom',
                'attr'=> [
                    'placeholder'=> 'Indiquer votre prenom'
                ]
            ] )
            ->add('lastname', TextType::class, [
                'label'=> 'Votre nom',
                'attr'=>[
                    'placeholder'=>'Indiquer votre nom'
                ]
            ])
            ->add('submit', TypeSubmitType::class, [
                'label'=> 'Valider',           // le nom du champs 
                'attr'=>[                      // les attributs du champ 
                    'class'=>'btn btn-success' // changement de la couleur par défaut 
                ]

            ])
        ;
    }
    // le tableau des options de configuartions 
    // paramettre de configuration sur l'entité globale du formulaire 
    // est-ce que par exemple l'email de User est bien unique dans la BDD 
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints'=> [                                       // tableau de contraintes 
                        new UniqueEntity([                          // pour un objet unique qui sera
                                    'entityClass'=> User::class,    // basé sur l'entité User 
                                    'fields'=>'email'               // et que c'est le champ email qui doit être unique en BDD
                        ])
            ],
            'data_class' => User::class,
        ]);
    }
}
