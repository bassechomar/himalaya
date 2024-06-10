<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{   // route connexion user 
    #[Route('/connexion', name: 'app_login')] // '/connexion' à la place de '/login' 
    public function index(AuthenticationUtils $auth): Response
    {   
        // gerer les erreurs des utlisateur 
        $error = $auth->getLastAuthenticationError();
        // Dernier username (email) // pour la pré-saisie de l'email de user 
        $lastUsername = $auth->getLastUsername();

        return $this->render('login/index.html.twig', [
            // 'controller_name' => 'LoginController',
             // passage des variables a twig 
            'error'=> $error ,
            'last_username'=> $lastUsername
        ]);
    }
    // route deconnexion // dans la doc symfony 
    #[Route('/deconnexion', name:'app_logout', methods: ['GET'])] // cette route n'accepte qu'une methode en GET // impossible de soumettre un formulaire
     
    public function logout():never 
     {
        throw new \Exception( message : 'Don\'t forget to activate logout in security.yaml');
     }
}
