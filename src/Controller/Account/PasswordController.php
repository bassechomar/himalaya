<?php

namespace App\Controller\Account;

use App\Form\PasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class PasswordController extends AbstractController

{   // déclaration de la l'attribut $entityManagerInterface pour hydrater le constructeur de la class avec
    private $entityManagerInterface;
    //et on hydrate le constructeur 
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }
//--------------------PASSWORD--------------------------------// 
#[Route('/compte/modifier-mot-de-passe', name: 'app_account_modify_pwd')] // '/compte' remplace '/account' (dans route)
// mis a jour de la BDD // pour la modification de mot de passe 
public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
{    // on va chercher l'utilisateur en courant 
    $user = $this->getUser();
    //test
    // dd($user);
    // on créatin du formulaire de modification avec injection de la  class PasswordUserTyper // on passe en paramettre l'instance $user   
    $form= $this->createForm( PasswordUserType::class, $user ,[
         //création de clé 'passwordHasher' pour passer le $passwordHasher dans la class PasswordUserType.php 
         // cela permet de passer l'injection de dependance de linterface UserPasswordHascherInterface // qui permet de hacher les mot de passe   
        'passwordHasher'=> $passwordHasher,
    ]);
    // ecoute de la requete
    $form->handleRequest($request);
    //test de validité de la requete 
    if($form->isSubmitted() && $form->isValid()) {
        // dd($form->getData());
        $this->entityManagerInterface->flush();
        // les message flash 
         $this->addFlash(
        'success', // typage du message ref: class alert bootstrap ==>alert ==> bcp de sens en UX 
        'Votre mot de passe est correctement mis à jour' // le message au template 
        );  
    }     
    return $this->render('account/password/index.html.twig', [
       'modifyPwd'=> $form->createView(),
    ]);
}
}

?>