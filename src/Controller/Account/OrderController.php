<?php

namespace App\Controller\Account;

use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{   // ce controller sert à afficher les dernières commandes de l'utilisateur
    // selon les regles de securtés defini dans l'app quand l'url a l'annotation '/compte' l'utlisateurdoit etre connecté 
    
    #[Route('/compte/commande/{id_order}', name: 'app_account_order')]

    public function index($id_order, OrderRepository $orderRepository): Response
    {
       $order = $orderRepository->findOneBy([
         'id'=>$id_order,
         'user'=>$this->getUser() // c'est eviter changer dans l'URL et saisir une id qui ne serait l'id de sa commande
       ]);
       // condition d'existence de la commande // si jamais la requête ne donne lieu a aucun resultat // redirection de l'utilisateur vers la home page 
       if(!$order){
        return $this->redirectToRoute('app_home'); 
       }

        return $this->render('account/order/index.html.twig',[
            'order'=>$order, 
        ]);
    }
}
