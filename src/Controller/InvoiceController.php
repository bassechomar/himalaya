<?php

namespace App\Controller;
use Dompdf\Dompdf;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InvoiceController extends AbstractController
/**
 * IMPRESSION FACTURE PDF pour un utilisateur connecté 
 * IMPRESSION FACTURE PDF pour un administrateur connecté 
 * Vérification de la commande pour un utilisateur donné 
 */
{   // Ici pour que l'utilisateur puisse consulater et imprimer la facture d'une de ses commande
    // il faut etre connecté et une fois connecté il sera sur la route /compte definit dans security.yaml
    #[Route('/compte/facture/impression/{id_order}', name: 'app_invoice_customer')]
    // injection de dependances OrderRepository 
    public function printForCustomer(OrderRepository $orderRepository, $id_order): Response
    {      // on va chercher dans le repository la commande concernées 
            $order = $orderRepository->findOneById($id_order);
            // Tester l'existence de la commande et/ou d'appartenance de la commande a User connecté 
            if(!$order || $order->getUser() != $this->getUser()){
                return $this->redirectToRoute('app_account');
            }
            // si ok il nous reste à envoyer $order au renderView()
                
            
            // Se rendre sur le depôt git et copier le QuickStart 
            $dompdf = new Dompdf();
            // definition d'une variable $html qui permettra de recuperer la vue twig dans templates 
            $html = $this->renderView('invoice/index.html.twig', [
                'order'=> $order,
            ]);
            $dompdf->loadHtml($html);  // On donne en argument de la méthode loadHtml() la va variable 
           
            $dompdf->setPaper('A4', 'portrait');   // (Optional) Setup the paper size and orientation
           
            $dompdf->render(); // Render the HTML as PDF
            // Output the generated PDF to Browser
            $dompdf->stream('facture.pdf', [
                'Attachment'=>false,
            ]);
            exit(); // pour stopper le processus 
        // return $this->render('invoice/index.html.twig', [
        //     'controller_name' => 'InvoiceController',
        // ]);
    }
    // Route pour Admin connecté ........// 

    #[Route('/admin/facture/impression/{id_order}', name: 'app_invoice_admin')]
    // injection de dependances OrderRepository 
    public function printForAdmin(OrderRepository $orderRepository, $id_order): Response
    {      
            $order = $orderRepository->findOneById($id_order);

            if(!$order){ // pour le cas de l'admin nous avons juste besoin de tester si la commande existe 
                return $this->redirectToRoute('admin');
            }
            $dompdf = new Dompdf();
            // ici on laisse invoice/index.html.twig c'est plus simple plutôt que créer un nouveau template juste pour l'admin 
            $html = $this->renderView('invoice/index.html.twig', [
                'order'=> $order,
            ]);
            $dompdf->loadHtml($html);  // On donne en argument de la méthode loadHtml() la va variable 
           
            $dompdf->setPaper('A4', 'portrait');   // (Optional) Setup the paper size and orientation
           
            $dompdf->render(); // Render the HTML as PDF
            // Output the generated PDF to Browser
            $dompdf->stream('facture.pdf', [
                'Attachment'=>false,
            ]);
            exit(); // pour stopper le processus 
    
    }
}
