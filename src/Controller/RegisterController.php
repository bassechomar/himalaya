<?php

namespace App\Controller;

use App\Class\Mail;
use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager ): Response
    {   
        $user = new User();
        // dd($request); 
        // on appelle le formulaire RegisterUserForm
        $form = $this->createForm(RegisterUserType::class, $user );
        
        //$registerForm ecoute la requete 
        $form->handleRequest($request);
        // si le formulaire est soumis 
        if($form->isSubmitted() && $form->isValid()) {
            // dd($registerForm->getData()); //test debug 
            $entityManager->persist($user); // Entité user créé et qui est lié au formulaire  
            $entityManager->flush();
            // ajout de message flash 
            $this->addFlash(
                'success',
                'Vous etes maintenant inscrit ! Vous pouvez vous connecter à votre espace membre'
            );
            // Envoi d'un e-mail de confirmation d'inscription sur le site 
            // la place de $content on mettra le fichier "welcome.html" qui trouve dans src\Mail\welcome.html
            // Nous voulons que notre class Mail() aille chercher le fichier "welcome.html" tout seul et l'injecte et 'envoi via l'api Mailjet  
            $email = new Mail();
            $vars = [
                'firstname'=>$user->getFirstname(),
            ];
            $email->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(),"Bienvenue sur Himalaya", "welcome.html", $vars); // $content est remplacé "welcome.html" 
            // redirection vers la page de connexion 
            return $this->redirectToRoute('app_login');
        }
        // Enregistre les datas en BDD
        // Envoi du message de confirmation du ompte bie créé 
        return $this->render('register/index.html.twig', [
          'form'=>$form->createView(),
        ]);
    }
}
//createForm(ClassForm:class) => crée le formulaire que le met ensuite dans une variable 
//createView()
//use => veut dire utilise cette espace de nom 
//namespace => ce sont des dossier pour eviter des collusion // on defit le repertoire le repertoire 
// 