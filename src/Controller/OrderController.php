<?php

namespace App\Controller;

use App\Class\Cart;
use App\Entity\Order;
use App\Entity\Address;
use App\Form\OrderType;
use App\Entity\OrderDetail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class OrderController extends AbstractController
{    /*
      * - Première étapes du tunnel d'acchat 
      * - choix de l'adresse de livraison et du transporteur 
      */
    #[Route('/commande/livraison', name: 'app_order')]
    public function index(): Response
    {    //redirection User au cas ou il n'a pas d'adresse enregistré 
        $addresses = $this->getUser()->getAddresses();  
        
        if(count($addresses) == 0){ // c'est une collection d'objets on a besoin de compter le nombre d'adresses 
            return $this->redirectToRoute('app_account_address_form'); // redirection vers le formulaire de creation d'adresse s'il n'y a pas d'adresse dans la collection 
        }
        // prepation de la vue du formulaire OrderType // 
        // OrderType n'est pas lié a un User : donc on met ce paramettre à 'null' dans la fonction createForm
        $form = $this->createForm(OrderType::class, null, [
            'addresses'=> $addresses, // acces a cette methode relation entre user et les addresses 
            // le paramettre action permet d'indiquer une route à suivre au formulaire avant sa soumssion
            // pour le traiter dans la route  #[Route('/commande/recapitulatif', name: 'app_order_summary')] (voir route suivant)
            'action'=> $this->generateUrl('app_order_summary'),
        ]);
     
        // -- rendu-----/ 
        return $this->render('order/index.html.twig', [
            'deliveryForm' => $form->createView(),
        ]);
    }
 /**
  * 2ieme Etape du tunel d'achat 
  * Recapitulation de la commande de l'utilisateur 
  * Insertion en base de données 
  * Preparation du payement vers Stripe 
  */
    #[Route('/commande/recapitulatif', name: 'app_order_summary')] 
    // add() on va ajouter la commande en base de données // les ligneCommande // 
    public function add(Request $request , Cart $cart, EntityManagerInterface $entityManagerInterface): Response
    {   // securité au cas l'utilisateur veuille charger le même Url 
        // methode 'POST'  seulement pour faire passer le formulaire 
        if($request->getMethod() != 'POST') { // 
            //methods:['POST',] // le de rediriger l'user vers une page si par exemple il essaye de recherger la page 
            return $this->redirectToRoute('app_cart');
        }
        // on crée la variable $products qui représente les produits contenus dans le panier de l'utilisateur 
        $products = $cart->getCart() ; // stocke tous les produits du panier dans la variable $products 
        // ici nous avons besoin d'écouter le formulaire 
        // et pour cela nous avons bseoins de la Request d'ou l'injection de dependance 
        $form = $this->createForm(OrderType::class, null, ['addresses'=> $this->getUser()->getAddresses()]);

        $form->handleRequest($request);
        // ici on teste le formulaire 
        if($form->isSubmitted() && $form->isValid()){
        // si le test est ok => Stockoge des données dans la BDD 
        //  dd($form->get('addresses')->getData());
         // on definit une variable pour stocker l'objet address choisi pa l'utilisateur dans le formulaire $form 
       
         $addressObject = $form->get('addresses')->getData();
        // ici on construit la chaine de cractère de l'adresse de livraison de la commande 
        $address = $addressObject->getFirstname().' '.$addressObject->getLastname().'<br/>';
        $address .=$addressObject->getAddress().'<br/>';
        $address .=$addressObject->getPostal().' '.$addressObject->getCity().'<br/>';
        $address .=$addressObject->getCountry().'<br/>';
        $address .=$addressObject->getPhone();
        // on envoi cette la variable [$address] à la variable delivery [$order]
        //ici on construit les données de la commande 
        // garder une trace des paniers abanadonnés par le utilisateurs 
            // dd($form->getData('carriers'));
        $order = new Order(); 
        // on dit a symfony a qui appartient cette commande // l'utilisateur en cours 
        $order->setUser($this->getUser());
        
        $order->setCreatedAt(new \DateTime());
        $order->setState(1);
        $order->setCarrierName($form->get('carriers')->getData()->getName()); 
        $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
        $order->setDelivery($address); 
        // pour chaque OrderDetails(ligneComande) créer les details du produit commandé 
        foreach($products as $product ) {
            $orderDetail = new OrderDetail();
            $orderDetail->setProductName($product['object']->getName());
            $orderDetail->setProductImage($product['object']->getImage());
            $orderDetail->setProductPrice($product['object']->getPrice());
            $orderDetail->setProductTva($product['object']->getTva());
            $orderDetail->setProductQuantity($product['qty']);
            $order->addOrderDetail($orderDetail);  // ici nous avons une erreure // il faut définir les permissions de cascade 
        }
        // on enregistre l'ensemble des details de la commande dans la base de données
        // donc ce qui veut dire que nous pouvons acceéder à ses données depuis ce controller pour les envoyer à stripe 
        // insertion en base de données 
        $entityManagerInterface->persist($order);
        $entityManagerInterface->flush(); 

        }
        return $this->render('order/summary.html.twig',[
            'choices'=> $form->getData(),
            'cart'=> $cart->getCart(),
            'order'=>$order,
            'totalWt'=>$cart->getTotalWt() 
        ] );
    }
}
