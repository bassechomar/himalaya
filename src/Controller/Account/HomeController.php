<?php

namespace App\Controller\Account;


use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// creation de controller AccountController pour uniquement afficher des vues aux users dans son espace membre
class HomeController extends AbstractController
{  
    #[Route('/compte', name: 'app_account')] // '/compte' remplace '/account' (dans route)
    public function index(OrderRepository $orderRepository): Response
    {
        // ici on va chercher toutes commandes effectuées dans le passé par l'utilisateur 
        // selon le tableau de conditions // user = soit le user courant // et statut soit 2 soit 3 
        $orders = $orderRepository->findBy([
            'user'=> $this->getUser(),       // cette requete ne prendra en compte les commande validée à savoir les commande qui auront leur statut 2 (vlidée)
            'state'=> [2,3] ,                // ici on passe un tableau avec les valeurs de state que peut avoir les commandes // cela filtre le resultat 
        ]);
        // test
        // dd($orders); 
        return $this->render('account/index.html.twig', [
            'orders'=> $orders , // renvoyé vers la page index de l'espace membre et bouclé sur le tableau mes dernieres commandes 
        ]);
    }
}
