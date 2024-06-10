<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{   //parametre qui va changer de page ne page : {slug} a indiquer a symfony au niveau de la route 
    // le {slug} doit etre donné en parametre des methodes de la class
    #[Route('/categorie/{slug}', name: 'app_category')]
    //  pour aller chercher le slug correspondant a une categorie donnée il nous faut faire injection de dependance
    //  CategorieRepository permet de faire de requetes sur la table category 
    public function index($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBySlug($slug);
        // $category = $categoryRepository->findOneByName('sacs français');
        // dd($category); 
        if (!$category){
            return $this->redirectToRoute('app_home');
        }
        return $this->render('category/index.html.twig', [
            'category' => $category,
        ]);
    }
}
