<?php

namespace App\Twig;

use App\Class\Cart;
use App\Repository\CategoryRepository;
use Twig\TwigFilter;
use Twig\Extension\GlobalsInterface;
use Twig\Extension\AbstractExtension;


// il faut implémenter l'interface GlobalsInterface pour utiliser la fonction getGlobals 
// il s'agira pour nos de créer les variables global twig pour gerer l'affichage de notre menu 
// non usages new*
class AppExtensions extends AbstractExtension implements GlobalsInterface
{
    // proprité 
    private $categoryRepository;
    private $cart;
    //-- contructeur prend en paramettre l'injection de dépendances des class Cart et CategoryReposittory
    // -- pour HYDRATER la class
    public function __construct(CategoryRepository $categoryRepository , Cart $cart)
    { // hydratation de notre  class 
         
       $this->categoryRepository = $categoryRepository; // on envoie $categoryRepository a la methode getGlobal() dont le return rend un tableau associtit 
       $this->cart = $cart;
    }
    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice'])
        ];
    }
    // creation de filtre 'price' pour formater tous les prix dans l'application
    public function formatPrice($number)
    {
        // nbre de virgule /, le séparateur virgule et la concatenation avec le signe €
        // la fonction number_format() est une fonction php 
        return number_format($number, '2', ','). ' €'; 
    }
    
    // definition d'une variable globals twig pour gerer le menu de navigation pour afficher les nos categories de produits
    // la fonction getGlobals() fait partie de l'interface GlobalsInterface as ExtensionGlobalsInterface
    public function getGlobals(): array 
    { // ici le pb : la fonction getGlobals() n'accepte pas l'injection de dépendance dont nous avons besoins
      // solution créer un __contruct() et y injecter un 'CategoryRepository' // et declarer une prorieté 'private $categoryRepository'
        return [
            'allCategories'=> $this->categoryRepository->findAll(),
            'fullCartQuantity'=> $this->cart->fullQuantity() // fonction créée dans Cart()
        ];
    }
   
}