<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Header;
use App\Entity\Carrier;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\SecurityBundle\Security\UserAuthenticator;
use Symfony\Component\Security\Http\Controller\UserValueResolver;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {    // on souhaite rediriger l'admin sur la hompage la première entité disponible 
        // return parent::index(); // page d'acceuil de dashbordController 
        return $this->render('admin/dashboard.html.twig');
        // Option 1. You can make your dashboard redirect to some common page of your backend
        // changer le OneOfYourCrudController::class ===> $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class)
    //$adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
    //return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
              
        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
           // ->setTitle('Himalaya'); // le nom du tableau de bord qui sera affiché 
            ->setTitle('<img src="assets/img/logo_himalaya.jpg" class="img-fluid d-block mx-0" style="max-width:50px; width:100%;  "><h2 class="mt-3 fw-bold text-gray text-left"> Himalaya </h2>');
    }
    // la méthode configureMenuItems() permet de gérer les différents items du menu du dashboard  
    public function configureMenuItems(): iterable
    {   yield MenuItem::linkToUrl('Accueil', 'fa fa-home' , '/');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-table');
        // pour avoir le menu à droite sous items Dashbord // 
        // signature :  yield MenuItem::linkToCrud(label:'Utilisateur', icon:'fas fa-list', entityFqcn: User::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Categories', 'fa fa-tags', Category::class);
        yield MenuItem::linkToCrud('Produits', 'fas fa-list', Product::class);
        yield MenuItem::linkToCrud('Transporteurs', 'fas fa-car', Carrier::class);
        yield MenuItem::linkToCrud('Commandes', 'fas fa-shopping-cart', Order::class);
        yield MenuItem::linkToCrud('Header', 'fas fa-image', Header::class);

    }
}
