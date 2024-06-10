<?php

namespace App\Controller\Account;

use App\Class\Cart;
use App\Entity\Address;
use App\Form\AddressUserType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AddressController extends AbstractController 

{    // passer une dependance dans le constructeur 
    // de sorte a pouvoir l'utliser partout dans les methodes feront appel à cette dependance 
    private $entityManagerInterface;
    // on hydrate le constructeur 
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }
    //-------------ADDRESS-------liste de tous les adresses de l'utilisateurs-----------------------------// 
    #[Route('/compte/adresses', name: 'app_account_addresses' )] 
    public function index(): Response
    {
        return $this->render('account/address/index.html.twig', []);
    }
//-----------------ADDRESSS DELETE -------------------------------------------//
   #[Route('/compte/adresses/delete/{id}', name: 'app_account_address_delete')]
  
   public function delete($id, AddressRepository $addressRepository): Response
   {
        $address = $addressRepository->findOneById($id);
        // test d'existence ou/et d'appartenance de de l'objet $address à l'utilisateur en cours 
        if(!$address OR $address->getUser() != $this->getUser()){   
            // retour si la condition est satisfaite. 
             return $this->redirectToRoute('app_account_addresses');
        }
        //message flash du traitement de suppression
        $this->addFlash(
            'success',
            'Votre adresse est correctement supprimé'
        );
         //Ensuite traitement en BDD // enregistrement de la suppression de l'adresse 
        $this->entityManagerInterface->remove($address);   
        $this->entityManagerInterface->flush(); 
 
        // redirection après suppression de l'adresse vers la liste des adresses de l'utlisateur courant 
        return $this->redirectToRoute('app_account_addresses');
   }
// ------- AJOUTER-CREER-- ADDRESS-USER-TYPE---formulaire de notre adresse---------//
// Pour créer une route pour Modifier une addresse existante
// on met parametre de la route un $id qui sera optionnel en le cas d'une creation ($id = null)
    #[Route('/compte/adresse/ajouter/{id}', name: 'app_account_address_form', defaults: ['id'=> null])] 
    // ici {id} => $id = null pour la création d'une / il sera utilisé par contre pour la modification d'adresse
   
    public function form(Request $request, $id , AddressRepository $addressRepository, Cart $cart): Response
    {   
        // le formulaire est déjà au courant qu'il est lié a mon entité Address()  
        if($id){
            $address = $addressRepository->findOneById($id);
            // aspect securité // l'id de l'adresse est transporté dans l'url / pas securisant si un malveillant venait à changer l'id dans l'url 
            // on teste d'abord si l'adresse appartient bien à l'utilisateur en cours 
            if(!$address OR $address->getUser() != $this->getUser()){ // si l'address n'existe pas ou que l'addresse n'appartient pas à l'utilisateur en cours 
                // die('ok');
                return $this->redirectToRoute("app_account_addresses");
            }
        }else{
            $address = new Address();
             // il faut préciser a symfony a qui appartient cette adresse que nous allons créer
             //et l'utilisateur es le User en cours  
            $address->setUser($this->getUser()); 
            // getUser() est une methode créée dans la class Address() // qui va chercher le User a qui appartir l'adresse
        }
        // creation du formulaire ---- -
        $form = $this->createForm(AddressUserType::class, $address);
        // ecoute de la requete  
        $form->handleRequest($request);
        // test 
        if($form->isSubmitted() && $form->isValid()){
            // on persist dans une création d'objet 
            $this->entityManagerInterface->persist($address);
            // enregistrement e base dedonnées 
            $this->entityManagerInterface->flush();
             // messsage flah
            $this->addFlash(
                'success',
                'Votre demande a bien été pris en compte avec succés'
             );
             // redirection apres creation de l'adresse User vers le panier si le panier contient des produit 
             // pour savoir si le anier contient des produit ==> une injection de dependance Cart() dans la methode form  
             if($cart->fullQuantity() > 0){
                // si le nombre de produit dans le panier est superieur a 0 // => redirection vers la commande  
                return $this->redirectToRoute('app_order');
             }
            return $this->redirectToRoute('app_account_addresses');
        }
        // on renvoi le tout a la vue twig 
        return $this->render('account/address/form.html.twig', [
            'addressForm'=>$form, 
        ]);
    }
}

?>