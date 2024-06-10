<?php

namespace App\EventSubscriber;

use App\Entity\Header;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;

class DeleteEasyAdmin implements EventSubscriberInterface
{
    public static function getSubscribedEvents():array
    {
        return [
            AfterEntityDeletedEvent::class => ['postRemove'],
        ];
    }
    // la fonction qui permet de gerer 
    public function postRemove(AfterEntityDeletedEvent $event):void
    {
       $entity = $event->getEntityInstance();

      $this->logActivity('remove', $entity);
    }
        
    public function logActivity(string $action, mixed $entity) 
    {    
        //dd($entity);
         // traitement de suppression d'images pour cas des entités Product 
        if (($entity instanceof Product) && $action === "remove") {  
            //remove image      
            //dd($entity->getImage());
            $filename = $entity->getImage();
            // suppression de l'image se trouvant dans public/uploads.$filename 
            $filelink= "../public/uploads/".$filename;
            //dd($filelink);
            try{
                $result =  unlink($filelink);
            }catch(\Throwable $th){
                
            }
           
            return;
        }
        // traitement de suppression d'image pour le cas des entités Header 
         if (($entity instanceof Header) && $action === "remove") {  
           //remove Header image  
             $filename = $entity->getImage(); 

             $filelink = "../public/uploads/".$filename; 

             unlink($filelink);

             return;
         }
    }        
 
}