<?php

namespace App\Controller;

use App\Class\Mail;
use App\Repository\UserRepository;
use App\Form\ForgotPasswordFormType;
use App\Form\ResetPassWordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ForgotPasswordController extends AbstractController
{   // Nous avons besoins de l'entiy manager dans les deux methodes de la class 
    // Alors c'est plus simple de le passer dans le constructeur directement 
    private $em ;
    public function __construct(EntityManagerInterface $em){
        $this->em =$em; // cela permet d'utiliser l'EntiyManager au sein de objet et de flush() simplement 
    }
    
    // meme titre que RegisterController // on crée un Controller pour le cas d'un mot de passe oublié 
    #[Route('/mot-de-passe-oublie', name: 'app_password')]
    public function index(Request $request, UserRepository $userRepository): Response
    {   // 1 . Formulaire 
        $form = $this->createForm(ForgotPasswordFormType::class);
        // ->On ecoute la requete 
        $form->handleRequest($request);
        // 2 . Traitement // nous avons beosoin d'écouter la requete pour cela donc on injecte la request 
        if($form->isSubmitted() && $form->isValid() ){
        // 3 . Si l'email renseigné par le user est en base donnée // injection de UserRepository
           $email = $form->get('email')->getData(); // pour recuperer juste la chine de caractere et pas le tableau complet de la request
           $user = $userRepository->findOneByEmail($email); // on va chercher dans la bd le user par le biais de son email 
           //dd($user); 
            // envoi d'un message flash un peu vague pour dire a l'utilisateur pour des question de sécurité 
            // si l'email n'existe pas on ne fera aucun traitement // pour éviter les users mal intentionnés
        // 4 .Envoi un message de notification à l'utilisateur 
           $this->addFlash('success' ,"Si votre adresse email existe, vous recevrez un email pour réinitialiser votre mot de passe ");
         // 5 . Si user existe, on reset le password, et on lui envoie par email le nouveau mot de passe 

           if($user){
             // 5 . a - Créer un token qu'on va stocker en BBD 
             // random_bytes() fonction php dont on passe un nombre d'octet qu'on souhaite genéré aléatoirement 
             // bin2hex() autre fonction php qui rend transformera ces octets générés en une chaine de caractere lisible 
             $token = bin2hex(random_bytes(15)); 
              // dd($token); // avons bien une chaine de carctère // et si on rafraichit nous avons une chaine de carctere completement differente de la première 
            // ici on affecte la valeur du token au user  // 
              $user->setToken($token); 
                // Validité du token dans le temps // on modifie la date actuelle et on rajoute 10 minutes
              $date = new \Datetime();
              $date->modify('+ 10 minutes'); // on ajoute 10 minutes à l'heure actuelle // correspondant à la durée de validation du token de réinitialisation 
                //dd($date);
              $user->setTokenExpireAt($date); // 
              // enregitrement des deux données en BDD
              $this->em->flush(); // [em] est déclaré dans le constructeur 
             // dd($user);
             // A cette étape nous avons besoin de générer l'url 
             // $url = $this->generateUrl('app_password_update', ['token'=>$token],UrlGeneratorInterface::ABSOLUTE_URL); // on tranporte tout l'url avec le https 
            $email = new Mail();
            // Pourquoi un token Une maniére traditionnelle la plus securiser pour modifier son mot de passe  
            $vars = [ // concepte des tokens // tableau des variables qu'on envoie à l'utilisateur 
                'link'=> $this->generateUrl('app_password_update', ['token'=> $token] , UrlGeneratorInterface::ABSOLUTE_URL), // envoi d'un lien absolu comprenant l'entête https à l'utilisateur 
            ];
            //test -------
            //dd($vars);

            $email->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(),"Modification de votre mot de passe",  "forgotpassword.html", $vars); // $content est remplacé "forgotpassword.html" 
            // redirection vers la page de connexion 
            //dd($e);
            //return $this->redirectToRoute('app_login');
            
           }
        }

        return $this->render('password/index.html.twig', [
             'forgotPasswordForm' => $form->createView(),
        ]);
  }  
  // Ici on crée une nouvelle route pour le nouveau mot de passe 
  // token = jeton d'acces // JWT = JSON WEB Tokens // un standard une forme de convention non abordé dans le cadre de cette formation 
  // Nous on va utiliser une version simplifier de création de chaine un tokenisése qu'on va stocker en base de données que l'on va verifier va verifier l'expiration et l'authenticité 
  #[Route('/mot-de-passe/reset/{token}', name: 'app_password_update')]
  public function update(Request $request, $token , UserRepository $userRepository):Response
  {  // on va cherche en BDD le $token pour voir si il est conforme 
    if(!$token){
       return  $this->redirectToRoute('app_password'); 
    }
    $user = $userRepository->findOneByToken($token); 
    // verification si l'utilisateur existe en bdd // et si l'utilisateur n'existe pas  
    $now = new \Datetime();
    if(!$user || $now > $user->getTokenExpireAt() ){
        return $this->redirectToRoute('app_password');
    }
    // si l'utilisateur existe on compare la date d'expiration du token $tokenExpireAt à la date actuelle 
    //die('On est bon pour renouveler le mot de passe ');
    // dd($user); 
    $form = $this->createForm(ResetPassWordType::class, $user); // on déclare 'data_clas'=> User:class (optionsResolver) du formulaire.

    $form->handleRequest($request); // on écoute la requête 

    if($form->isSubmitted() && $form->isValid()) {
        // traitement à éffectuer pour modifier le mot de passe de l'utilisateur 
        //dd($form->getData()); 
        $user->setToken(null); // on reinitialise le token 
        $user->setTokenExpireAt(null); // pareil la date d'expiration à null leur valeur par défaut  
        $this->em->flush();
        $this->addFlash(
                'success',
                'Votre mot de passe est correctement mis à jour. Vous pouvez desormais vous connecter à votre compte '
        );
        return $this->redirectToRoute('app_login'); 
    }

    return $this->render('password/reset.html.twig', [  
        'form'=> $form->createView(), 
    ]);
  }
}
