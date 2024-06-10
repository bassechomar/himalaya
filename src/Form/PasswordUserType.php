<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType as TypeSubmitType;

class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actualPassword', PasswordType::class, [
                'label'=>"Votre mot de passe actuel",
                'attr'=>[
                    'placeholder'=>'Indiquer votre mot de passe actuel'
                ],
                'mapped'=> false
                
            ])
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
            // répondre au deux question // pour controller le mot de passe actuel 
            // a quel moment ecouter // et quel traitement effectué 
            // reponse : ecouter lorsque le formulaire est soumis 
            // => appel a la class FormEvents qui se trouve dans Symfony\Component\Form //=> SubmitEvent de Symfony\Component\Form\Event
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event){
                //test 
                
                // la fonction de récuperation du formulaire lié à l'évènement 
                $form = $event->getForm();
                // $user = dans les configurations du formulaire nous avons les options et dans options nous avons les data de user  
                // correspondant a l'objet App\Entity\User 
                $user = $form->getConfig()->getOptions()['data'];
                // recuperation de la clé de tableau définit en parametre dans la methode createForm() dans le controller AccountController.php 
                $passwordHasher = $form->getConfig()->getOptions()['passwordHasher'];
                //encodage de se mot de passe $passwordHasher tres compliqué 
                // autre mehtode de comparation de mot de passe plainText et le mot de passe encodé en BDD 
                // la methode isPasswordValid() de l'interface UserPasswordHasherIntreface 
                $isValid = $passwordHasher->isPasswordValid(
                    $user,
                    //Récupération du password actuel saisi par le user
                    $form->get('actualPassword')->getData() // Nous n'aurons pas besoin d'aller chercher le mot de passe en base de données
                );

                // test nous permet de voir dans le $form->getConfig()->getOptions()['data'] mon 'passwordHasher' defini de le controller AccountController.php en troisième option de la methode createForm()
                // dd($form->getConfig()->getOptions()['passwordHasher']);
                //ce mot de passe actuel est en clair => et non encore envoyé en BDD // et stocké dans $actualPwd
                //1.Recuperation du password actuel saisi par le user 
                // $actualPwd = $form->get('actualPassword')->getData();
                
                //2.Récuperer le password actuel enregistré en BDD
                // Ce password est encodé en BDD // donc ça va être compliqué de comparer les deux s'il sont egaux 
                // Conseil pour se faire // se rendre dans la Doc => composant Security => Password => 
                //  $actualPwdDatabase = $user->getPassword();
                //test
                // dd($isValid); 
                //3.Si c'est != envoyer une erreur 
                if(!$isValid){
                    // ajouter à la volet des messages d'erreur à nos input en allant chercher le champs concerné par l'erreur 
                    // l'objet FormError 
                    $form->get('actualPassword')->addError(new FormError('Votre mot de passe actuel n\'est pas confrome , Veuiller saisir un mot de passe valide'));
                    // Il reste un chose à faire c'est de mettre a la base données à jour 
                }
               
            } )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,    // le formulaire fait le lien entre le User 
            'passwordHasher'=> null        //  AccountController.php ==> dans createForm() ==> 3ieme argument de la methode ==> ['passwordHasher'=> $passwordHasher] 
                                           // cette clé existe desormais dans les option de configurations du formulaire et accessible via les methodes $form->getConfig()->getOptions()['passwordHasher'] 
        ]);
    }
}
