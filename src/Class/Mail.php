<?php
// declaratin de l'espace de nom 
namespace App\Class; 

use Mailjet\Client;
use Mailjet\Resources;

// creation de la class Mail()
class Mail
{   // creation d'une methode qui ne servirait juste pour envoyer des emails
    // confgurer la methode send() : lui fournir des paramettre comme: l'adresse email du destinataire / en deuxieme parametre  
    // ex : $mail->send('monclient@gmail.com', 'Nom du client', 'Sujet du mail', 'le contenu de notre mail') 
    // au total 4 parametres par exemple pour la methode send() // pour simplifier cette tache d'envoi d'email 
    public function send($to_email, $to_name, $subject, $template, $vars = null) //public function send($to_email, $to_name, $subject, $content) 
    {       // Recuperation du template "welcome.html" 
            // Pour ce faire remplacer la variable $content dans la methode send() et le remplacer par $template 
        //dd(dirname(__DIR__)); // dirname — Renvoie le chemin du dossier parent //
            // Nous aurons besoins de remonté d'un niveau (pour aller a la racine du dossier "src") pour aller chercher le fichier "welcome.html" 
        $content = file_get_contents(dirname(__DIR__).'/Mail/'.$template); // la récuperation du template de mail à partir du dossier src/Email
        // recuperations des variables facultatives
        if($vars){
            foreach($vars as $key=> $var){
               $content =  str_replace('{'.$key.'}' , $var , $content);
            }
        }

        // dd($content);
        // On recupere tout le contenu qui a servi de test dans la class HomeController
        // importation des class 
        $mj = new Client($_ENV['MJ_APIKEY_PUBLIC'], $_ENV['MJ_APIKEY_PRIVATE'], true, ['version'=>'v3.1']);
        // depuis GitHub copier tout le body qui definit un certain nombre d'informations pour envoyer un premier email 
        $body = [
            'Messages' => [
                [ // deux adresses email: expediteur et destinataire // mailjet deconseille d'utiliser par defaut notre adresse email d'inscription 
                  // dans notre cas actuel ça sera l'adresse email qui nous avons utilisé pour créer notre compte 
                    'From' => [
                        'Email' => "bassechomar@gmail.com",
                        'Name' => "wohioo" // idéal dans l'avenir mettre le nom de notre site internet a cet endroit 
                    ],
                    'To' => [
                        [
                            'Email' => $to_email , // la variable $to_mail // remplace la valeur en dure "heuch1980@yopmail.com"
                            'Name' => $to_name // le nom et prénom du destinataire 
                        ]
                    ],
                    'TemplateID'=> 5937036, // c'est le numéro du template que nous avons créé dans Mailjet
                    'TemplateLanguage'=> true, // par defaut mettre toujours à 'true' ça va ensemble avec leur variable de templating de Mailjet
                    'Subject' => $subject, // la variable $subject
                    'Variables'=>[  // ne pas suivre la documentation // il faut ouvrir un tableau et associer à la clé 'content'=>la variable $content
                        'content'=>$content,
                    ],
                   
                    //'TextPart' => "Greetings from Mailjet!",
                    // Dans ce qui suis on retire 'HTMLPart'=>$content qui n'est plus necessaire 
                    // car ce n'est plus un contenu au format HTML mais plutôt un contenu au format text 
                    // qui peut etre du html ou bien contenir du html // mais qui va venir se remplacer automatiquement  
                    // 'HTMLPart' => $content // 
                ]
            ]
        ];
        ///--- on retire la variable $response // car nous en avons pas besoin en état ----// 
        // plus bas dans la doc github on tombe sur une variable $response  // pour envoyer le message 
        // importer la class Resources ==> use [Mailjet\Resources;]
          $mj->post(Resources::$Email, ['body'=>$body]); // ici on ne conserve que la variable $mj 
        // une derniere etapes // => pour pouvoir lire la reponse 
       // $response->success() && var_dump($response->getData());
    }


}