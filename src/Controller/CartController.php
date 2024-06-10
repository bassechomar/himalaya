<?php
// Le CartController gere toutes les routes de mon pannier 
namespace App\Controller;


use App\Class\Cart;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    #[Route('/mon-panier/{motif}', name: 'app_cart', defaults: ['motif' => null])]
    public function index(Cart $cart, $motif): Response
    {    // gestion des annulation de commandes depuis stripe // nous rajoutons un paramettre {motif} 
        // comme motif de l'annulation de la commande et nous le mettons a null par defaut 
            if($motif == "annulation") {
                $this->addFlash(
                    'info',
                    'Paiement annulé: Vous pouvez maintenant mettre à jour votre panier et votre commande'
                );
            }

         // injection de dépendance de la class Cart() 
         // pour envoyer le contenu du panier à la vue du panier 
         // création de la methode getCart() dans la Class Cart()
        return $this->render('cart/index.html.twig', [
           'cart'=>$cart->getCart(),
           'totalWt'=> $cart->getTotalWt()
        ]);
    }
//-----------  creation d'une route secondaire pour ajout de produit au panier 
    
    #[Route('/cart/add/{id<\d+>}', name: 'app_cart_add')]
    public function add($id, Cart $cart, ProductRepository $productRepository , Request $request  ): Response     
    {   // dd($request->headers->get('referer'));
        // injection de dependance de la classe Cart() dans la methode add() // De ce fait methode ne fonctionnera qu'avec la Class Cart uniquement  
        // au lieu de passer l'id du produit on passera l'objet complet product dont l'id est en parametre // ce choix est justifié par le besoin de pouvoir acceder à d'autre propriétés de l'objet en question (name, image,...)    
        $product = $productRepository->findOneById($id);
        // On recupere la variable injectetion de dependance de la class Cart() on lui ajoute le resultat de la requete $produit = $ProductRepository->findOneById($id)  
        // Desormais dans notre session nous aurons un objet product résulatt de cette requêtte qu'on peut récuperer dans la methode add() de la class Cart()
        $cart->add($product); // 
        // pour garder le choix de l'utilisateur de garder un objet product dans son pannier il faut bien le stocker quelque part 
        // pas en base de donnée etant donnée qu'un pannier a une durée de vie et c'est plutot transitoire 
        
        // dd('Produit ajouté au panier '); //test
        // Ajout de msg flach 
        $this->addFlash(
          'success',
           'Votre produit est bien enregistré dans votre panier . Vous pouvez en rajouter d\'autre dans votre panier' 
        );
        // rediriger le user vers la page precedante (injection de Request pour recuperer les headers(entêtes) pour acceder à la referer => la page precedante )
       return $this->redirect($request->headers->get('referer')); 

       //// return $this->redirect('/categorie/'.$product->getCategory()->getSlug());

        // dans ce cas précis nous n'avons pas vraiment de vue à servir 
       // la methode add() sert juste à jouter un produit au panier de l'utilisateur  
       
       // return $this->render('cart/index.html.twig', [
        // ]);
    }
//-----------DECREASE PRODUIT PANIER ---- DIMINUER LE PRODUIT-------------------------//
 
#[Route('/cart/decrease/{id<\d+>}', name: 'app_cart_decrease')]
    public function decrease($id, Cart $cart , Request $request ): Response       
    {   
    // décrémenter un produit de notre panier // cette methode est créée dans la classe Cart()
    $cart->decrease($id);
    // message flash de retour 
    $this->addFlash(
        'success',
        'Votre produit a bien été supprimé de votre panier' 
        );
        // rediriger le user vers la page précedante (injection de Request pour recuperer les headers(entêtes)// et ensuite accéder à la referer => la page precedante )
        return $this->redirect($request->headers->get('referer'));
    }  
  
    //---REMOVE PANIER ------VIDER LE PANIER APRES PAYEMENT DE LA COMMANDE PAR LE USER -----------------// 
#[Route('/cart/remove', name: 'app_cart_remove')]
   
    public function remove(Cart $cart): Response     
    { // cette methode remove() a été créée dans la class Cart() 
      // Elle a pour fonction de supprimer le panier('cart') en cours dans la session
        $cart->remove(); 

        return $this->redirectToRoute('app_home');
        
    }
}
