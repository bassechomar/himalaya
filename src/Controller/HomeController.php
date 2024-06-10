<?php

namespace App\Controller;

//use Mailjet\Client; 
//use Mailjet\Resources;
use App\Class\Mail;
use App\Repository\HeaderRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')] //on modife la route ('')
    public function index(HeaderRepository $headerRepository, ProductRepository $productRepository): Response 
    {   // echo 'bonjour'; die();
        /** TEST DE FORMULAIRE DE RECHERCHES DE PRODUITS  */
            
        
        // pour recuperer les header gerer par l'admin en base de données 


        //-- TEST Mailjet ---- a partir de la class Mail()
        // $mail = new Mail(); 
        // exemple de la variable $content avec des balises HTML // permet de personaliser et/ou formater nos emails 
        // $content = "Bonour Diambar <br/> J'espère que tu vas bien. <br/> Je t'adresse ce petit messsage depuis la Himalaya.<br> La boutique exotique saveurs d'ailleurs";
        // $mail->send('XXXX', 'XXXX', "C'est votre épicier du coin", 'welecome.html');
        // test de Mailjet // on instancie la class Client() de Mailjet 
        // // On nous parle de clé API // On nous suggere de définir des variables d'environnement 
        // // qui seront accessible par l'ensemble de nos controller // si dans l'avenir nous avons a changer des clés API 
        // // nous aurons a faire des modification a un seul endroit dans nos variables d'environnement 
        // $mj = new Client($_ENV['MJ_APIKEY_PUBLIC'], $_ENV['MJ_APIKEY_PRIVATE'], true, ['version'=>'v3.1']);
        
        // // depuis GitHub copier tout le body qui definit un certain nombre d'informations pour envoyer un premier email 
        // $body = [
        //     'Messages' => [
        //         [ // deux adresses email: expediteur et destinataire // mailjet deconseille d'utiliser par defaut notre adresse email d'inscription 
        //           // dans notre cas actuel ça sera l'adresse email qui nous avons utilisé pour créer notre compte 
        //             'From' => [
        //                 'Email' => "bassechomar@gmail.com",
        //                 'Name' => "Me" // ideal dans l'avenir mettre le nom d notre site internet a cet endroit 
        //             ],
        //             'To' => [
        //                 [
        //                     'Email' => "heuch1980@yopmail.com", // comme adresse email: nous allons utiliser une adresse email jetable Yopmail 
        //                     'Name' => "You" // le nom et prénom du destinataire 
        //                 ]
        //             ],
        //             'Subject' => "My first Mailjet Email!", //sujet 
        //             'TextPart' => "Greetings from Mailjet!",
        //             'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href=\"https://www.mailjet.com/\">Mailjet</a>!</h3>
        //             <br /> May the delivery force be with you!"
        //         ]
        //     ]
        // ];
        // // plus bas dans la doc git on tombe sur une variable $response  // pour envoyer le message 
        // // importer la class Resources ==> use [Mailjet\Resources;]
        // $response = $mj->post(Resources::$Email, ['body'=>$body]);
        // // une derniere etapes // => pour pouvoir lire la reponse 
        // $response->success() && var_dump($response->getData());

        // ----------
        return $this->render('home/index.html.twig', [ 
            'headers'=>$headerRepository->findAll(),
            'productsIsHomePage'=> $productRepository->findByIsHomePage(true)
        ]);
    }
}
