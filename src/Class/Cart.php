<?php 

namespace App\Class;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart 
{   // Docs->Basic Usage ->Session ->   
    public function __construct(private RequestStack $requestStack)
    {    // injection de dependance RequestStack qui nous permettra d'aller chercher notre session 
        // Acceder à la session depuis le constructeur n'est pas recommandé
        // Entraine des Effets de regression tout au long du developpement // selon la documentation  
    }
    
    public function add($product) 
     {  // dd($product);  
        // Ici definition d'une methode add() qui porte le même nom que celle definie dans le CartController
        // Cette méthode add() va permetre de répondre a la méthode add() créée dans le CartController 
        // $session = $this->requestStack->getSession();
        // dd($session);  
        // dd($product); // test
        
        // Pour stocker les objets ajouter au panier 
        // permet de recuperer le panier en cours et de lu reinjecter de nouvelles informations 
        // $cart = $this->requestStack->getSession()->get('cart');
        $cart = $this->requestStack->getSession()->get('cart');
       
        // dd($cart);
  
        // au lieu de l'id on recupere tout l'objet product pour traitement 
        // C'est à l'intérieur de cette methode que l'on va Appeler la session de symfony 
        
        // Et Ajouter une quantity +1 à mon produit 
        // Condition if pour prendre en compte un produit existant dans le panier et rajouter +1 a cette  quantité existante
        if(empty($cart[$product->getId()])){
            $cart[$product->getId()] = [
                'object' =>$product,
                'qty'=> 1
            ];
        }else{
            $cart[$product->getId()] = [
                'object'=>$product,
                'qty'=> $cart[$product->getId()]['qty'] + 1, 
            ];
        }
         // Puis Créer ma session Cart() 
         $this->requestStack->getSession()->set('cart', $cart);
         // test
        //  dd($this->requestStack->getSession()->get('cart'));
    }
//--------LA FONCTION  DECREASE ----------------//
    public function decrease($id){
        // on transporte $id depuis le CartController // ici c'est le seul paramêtre dont nous avons besoin 
        // acceder à 'cart' et le stocké dans la varible $cart 
        $cart = $this->requestStack->getSession()->get('cart');
        if ($cart[$id]['qty'] > 1 ){
              $cart[$id]['qty'] --; // décrementation si qty superieur a 1 
        }else{ // traitement du cas ou qty est egal à 1 (1-1)=0 
            unset($cart[$id]);
        }
        $this->requestStack->getSession()->set('cart', $cart); // mis à jour de la session 
    }
//--- fullQuantity pour récuperer la quantité total de produits existants dans panier  
    public function fullQuantity()
    {
        $cart = $this->requestStack->getSession()->get('cart');
        $quantity = 0;

        if (!isset($cart)){
            return $quantity;
        }

        foreach ($cart as $product){
            $quantity = $quantity + $product['qty']; 
        }
        return $quantity;      
    } 
//------------TOTALWT -------------------------//
    public function getTotalWt()
    {
        $cart = $this->requestStack->getSession()->get('cart'); // on recupere notre objet 
       
        $price = 0 ; // initialise de la variable 
        
        if (!isset($cart)){
            return $price;
        }

        foreach($cart as $product){
            $price = $price + ($product['object']->getPriceWt() * $product['qty']) ;
        };
        return $price ; // on retourne 
    }
//--------LA METHODE REMOVE --// --SUPRIME DE LA SESSION EN COURS l'entrée 'cart'----//
    public function remove(){ 
        return $this->requestStack->getSession()->remove('cart'); 
    }
//------- LA METHODE GETCART ----//--DONNE ACCES A LA 'cart' DE LA SESSION EN COURS--- // 
    public function getCart()
    { // cart est le nom de la session 
        return $this->requestStack->getSession()->get('cart');
    }
} 