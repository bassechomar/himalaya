<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    /** les differents statuts que peut prendre une commande 
     * 1 : En attente depayement 
     * 2 : Paiement validé 
     * 3 : Expedié 
     */
    #[ORM\Column]
    private ?int $state = null;

    #[ORM\Column(length: 255)]
    private ?string $carrierName = null;

    #[ORM\Column]
    private ?float $carrierPrice = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $delivery = null;

    /**
     * @var Collection<int, OrderDetail>
     */
    // Dans ce cas de figure nous avons besoin d'accorder des permissions de CASCADE à l'entité Order pour
    // manipuler l'entité OrderDetail // en y ajoutant l'annotation comme cascade:['persist'] 
    // le tableau définit les cas ou la permission de modification( de création) est accordée à l'entité Order 
    #[ORM\OneToMany(targetEntity: OrderDetail::class, mappedBy: 'myOrder', cascade:['persist'] )]
    private Collection $orderDetails;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $stripe_session_id = null;

    public function __construct()
    {
        $this->orderDetails = new ArrayCollection();
    }
    // cette méthode est utilisée dans le dashboard commande pour spécifie le Total T.T.C accessible par la clé 'totalWt'
    // elle sert à retourner le Total T.T.C dans le dashboard commande 
    public function getTotalWt(){
        // nous avons stocker en BDD le total Hors Taxe // pour les besoins de calcul 
        $totalWt = 0 ; 
        $products = $this->getOrderDetails(); 
        foreach($products as $product){
            $coef = 1 + $product->getProductTva()/100; 
            $totalWt += ($product->getProductPrice() * $coef) * $product->getProductQuantity();   // le prix unitaire TTC multiplié par le producQuantity  
        }
        return $totalWt + $this->getCarrierPrice(); // le prix transporteur est en T.T.C
    }
    // cette methode est également utilisée dans Admin/OrderCrudController accéssible par a clé 'totalTva'
    // elle sert a retourner le Total TVA dans le dashboard commande 
    public function getTotalTva(){
        $totalTva = 0 ; // on initialise la variable tva à 0 
        // pour calculer le TVA nous avons besoin de tous les orderDetails pour calculer le tva de chaque produit 
        $products = $this->getOrderDetails(); // c'est tableau/colection d'objets de product
        foreach ($products as $product){
            $coef = $product->getProductTva()/100; // methode dans OrderDetail.php
            $totalTva += $product->getProductPrice() * $coef; // methode dans OrderDetail.php
        }
        return $totalTva; 
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getCarrierName(): ?string
    {
        return $this->carrierName;
    }

    public function setCarrierName(string $carrierName): static
    {
        $this->carrierName = $carrierName;

        return $this;
    }

    public function getCarrierPrice(): ?float
    {
        return $this->carrierPrice;
    }

    public function setCarrierPrice(float $carrierPrice): static
    {
        $this->carrierPrice = $carrierPrice;

        return $this;
    }

    public function getDelivery(): ?string
    {
        return $this->delivery;
    }

    public function setDelivery(string $delivery): static
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * @return Collection<int, OrderDetail>
     */
    // cette permet de recuperer toutes lignes commandes (les OrderDetails)
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetail $orderDetail): static
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails->add($orderDetail);
            $orderDetail->setMyOrder($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetail $orderDetail): static
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getMyOrder() === $this) {
                $orderDetail->setMyOrder(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
 // Création de la l'id session stripe , nullable de base 
 // car avant que la commande ne soit payé sur Stripe cette l'id de la Session tripe n'est pas encore créé 

    public function getStripeSessionId(): ?string
    {
        return $this->stripe_session_id;
    }

    public function setStripeSessionId(?string $stripe_session_id): static
    {
        $this->stripe_session_id = $stripe_session_id;

        return $this;
    }
}
