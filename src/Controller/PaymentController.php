<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Class\Cart;
use Stripe\Checkout\Session;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    #[Route('/commande/paiement/{id_order}', name: 'app_payment')] // pour acceder au details d'une commande il nous l'id de commande
    public function index($id_order, OrderRepository $orderRepository, EntityManagerInterface $entityManagerInterface): Response
    {   
        // la clé API 
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $YOUR_DOMAIN = $_ENV['DOMAIN'];

        // id_order vient de order/sumary.thml.twig 
        // Pour recuperer l'ensemble des details de la commande nous avon de OrderRepository en injection de dependance dans la methode index 
        // dd($id_order); // debug de id_order
        // $order = $orderRepository->findOneById($id_order);
        
        // question securité // pour complexifier la requete // cela evite de passer par l'URL // avec un id_order differnet de celui du user courant 
        $order = $orderRepository->findoneBy([
            'id'=>$id_order, // conforme a l'id commande 
            'user'=>$this->getUser(), // qui appartient à l'utilisateur en cours  
        ]);
        // si jamais tu n'as pas de commandes redirige l'utilisateur vers la home page 
        if(!$order){ 
            return $this->redirectToRoute('app_home');
        }

        //dd($order); //test debug order 
        $product_for_stripe = []; // 
        // Nous avons fais un foreach dans le OrderController // nous etions passé de la session a la BDD
        // et dans cette boucle nous créons un orderDetail avec ceetertaine information// tout ce dont Stripe a besoin 
        foreach($order->getOrderDetails() as $product){ 
            $product_for_stripe[] = [ // on envoi cette variable une fois construite à stripe // $checkout_session  ==> au tableau 'line_items' 
                'price_data' => [
                    'currency'=> 'eur', 
                    // nous formatons ici le prix pour correspondre au format de prix attendu par stripe (ex: 15 € corresponderait a 1500)       
                    'unit_amount'=> number_format($product->getProductPriceWt() * 100, decimals: 0, decimal_separator:'', thousands_separator:''),  //stripe ne prend que des prix T.T.C
                    'product_data'=> [         
                       'name' => $product->getProductName(),
                       'images'=> [
                        // cette maniere de faire ne se limite qu'à un environnement local // non en production 
                         $_ENV['DOMAIN'].'/uploads/'.$product->getProductImage(), 
                       ]
                    ]
                 ], 
                 'quantity' => $product->getProductQuantity(),
            ];
            // dd($product);
        }
        // ici on construit le produit transporteur // dans stripe le transporteur est considéré comme un produit a part entière 
        $product_for_stripe[] = [ 
            'price_data' => [
                'currency'=> 'eur', 
                // le pix du transporteur est déjà en T.T.C nous n'avons pas besoins de le recalculer pour y ajouter la tva 
                // les données du transporteur sont disponibles dans la variable $order definit plus haut      
                'unit_amount'=> number_format($order->getCarrierPrice() * 100, decimals: 0, decimal_separator:'', thousands_separator:''),  //stripe ne prend que des prix T.T.C
                'product_data'=> [         
                   'name' => 'Transporteur :'.$order->getCarrierName(),
                ]
             ], 
             'quantity' => 1, // il n'ya qu'un transporteur à la fois 
        ];
        // dd($product_for_stripe); //test 
        
        // on garde les lignes suivantes histoire d'avoir une trace de modification et amelioration apportées  

        // dans la Doc =>checkout.php => \Stripe\Stripe::setApiKey($stripeSecretKey); // correpond à la ligne suivante
        // on copie colle la clé API secrete directement du fichier secrets.php
        // Stripe::setApiKey('sk_test_51PB6PrP27msa4YxHTvEAOqiAXZkDZMhB1sGuRiP2i70t6ZsiFYAXQ95bZ0Ca0NuwZCWyOriKFEKUDvkDSb9ZF2w000RhARrr57');
        // ensuite stripe nous qui peut definir notre domaine 
       //  $YOUR_DOMAIN = 'http://127.0.0.1:8001';
        // Ensuite il nous dit de créer un checkout session 
        // modifier $checkout_session = \Stripe\Checkout\Session::create // => use Stripe\Checkout\Session
        $checkout_session = Session::create([
             // pour saisir automatiquement l'email de l'utilisateur 
             'customer_email'=> $this->getUser()->getEmail(),
            // on peut gerer  directement nos produits dans stripe // catalogue produits // et le mettre a l'interieur de stripe 
            // mais ici nous sommes dans le cadre d'une backOffice personnalisé pour gerer le ecommerce du client
            // dans notre cas stripe s'occupera uniquement d'accepter les paiements 
            // et que toute la mecanique de produits soit envoyer a stripe par le tableau suivant (nom produi/prix/ voir même illustration)
            'line_items' => [[
            // Doc stripe /alle dans => Stripe Api => Checkout => Session => creer session => line_items => c'est un tableau de tableaux associatif
            // dans ce tableau line_items => differentes possibilités / => un tableau associatif price_data 
            //   'price_data' => [
            //      'currency'=> 'eur',        // la monaie utilisée
            //      'unit_amount'=> '1500',    // prix unitaire/100 (donc 1500 = 15.00 €) // on a besoin prix de l'entien sans la virgule pour que stripe decale 
            //      'product_data'=> [         // c'est aussi un tableau associatif 
            //         'name' => 'produit de test',
            //      ]
            //   ], 
            //   'quantity' => 1,
            // VARIABLE CONSTRUITE ET DONNEE au tableau 'line_items' de la variable $checkout_session=Session::create([....] 
            // Elle contient les informations de la commande /de ligne commande / ainsi que les infrmations sur les produits commandés 
            // le prix du transporteur qui egalement cosidéré comme un produi t a part entière 
                $product_for_stripe,
            ]],
            'mode' => 'payment',
            // ici on redirige aprés le payement l'utilisateur vers la '/commande/merci/{id sesson stripe} // 
            // Stripe a prevu une constante pour cela CHECKOUT_SESSION_ID // Effectuera le remplacement des valeurs automatiquement
            'success_url' => $_ENV['DOMAIN'] . '/commande/merci/{CHECKOUT_SESSION_ID}', 
            // gestion de la page annulation de commande // redirection de l'utilsateur vers son panier et commande
            'cancel_url' => $_ENV['DOMAIN'] . '/mon-panier/annulation',  
          ]);
          //Création de l'id de la session stripe // en pour l'enregistrer en base BDD nous avons de l'EntityManagerInterface en injection 
          $order->setStripeSessionId($checkout_session->id); // il ya certainement du getStripeSessionId() // ce qui veut dire qu'on peut fare passer dans twig cet identifiant 
          // enregitrement en base de données de l'id $checkout_session
          $entityManagerInterface->flush(); 

         // redirection de notre utilisateur // 
         // le header("location", .....) le marche pas ne symfony il nous ici return redirect
        //  header("HTTP/1.1 303 See Other");
        //  header("Location: " . $checkout_session->url);
        return $this->redirect( $checkout_session->url);
    }

    // Gestion de la page 'success_url'  et redirection lorsque le paiement d'une commande s'est reussi 
    #[Route('/commande/merci/{stripe_session_id}', name: 'app_payment_success')]
    public function success($stripe_session_id, OrderRepository $orderRepository, EntityManagerInterface $entityManagerInterface,Cart $cart):Response
    {   // Pour vider le panier apres l'achat de l'utilisateur // Nous injectons la class Cart() e ndependance de la methode success()
        // nous allons avoir besoin lorsqu'on redirige l'utilisateur vers la page de 'success' de récuperer la commande de l'autre coté comme $order = $orderRepository->find...
        // Est-ce plus judicieux de faire circuler via la session $checkout_session de Stripe l'id_order et recuperer ça lorsqu'il va  rediriger l'utilisateur vers la page 'succes_url'
        // Une autre possibilité est de stocker l'id de la session $checkout_session generée par Stripe (avec la methode Session::create() ) dans la BDD 
        // Pourquoi : cela permetra de suivre les payment en comptabilité dans le futur // de ne pas faire circuler l'id_order de page en page //
        // En outre cela permet de retrouver la commnde coté Admin en cas de probleme de payement et/ou de commandes 
         
        $order = $orderRepository->findOneBy([  // pour securiser le tout on donne deux arguments user en cours et la session stripe de l'order
            'stripe_session_id'=> $stripe_session_id,
            'user'=> $this->getUser()
         ]);
         // redirection si la commande n'existe pas vers home page 
         // c'est pour eviter que des user puisse acceder par hazard a des commandes qui ne leur appartiendra pas  
         if(!$order){
            return $this->redirectToRoute('app_home'); 
         }
         // On met a jour le statut de la commande de l'utilisteur 
         if($order->getState() == 1){ // à ce stade normalement le status de la commande est à 1 
            $order->setState(2);   // donc si c'es ta 1 on passe le statut de la commande 2 (payé)       
            // si le statut de la commande passe a 2(payé) on vide le panier / 
            $cart->remove(); 
            // nous avons besoin d'enregistrer l'etat de la commande en BDD donc de EntityManagerInterface en injection de dependance
            $entityManagerInterface->flush();
         }
       
        //   dd($order);
        return $this->render('payment/success.html.twig',[
            'order'=>$order, 
        ]);
    }
}
