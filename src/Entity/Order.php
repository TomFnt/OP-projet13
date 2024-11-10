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

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_order = null;

    #[ORM\Column]
    private ?float $totalPrice = null;

    #[ORM\Column()]
    private ?string $status = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: ProductOrder::class, mappedBy: 'order', orphanRemoval: true)]
    private Collection $productOrders;

    #[ORM\Column()]
    private ?string $orderNum = null;

    public function getOrderNum(): ?string
    {
        return $this->orderNum;
    }

    public function setOrderNum(?string $orderNum): Order
    {
        $this->orderNum = $orderNum;
        return $this;
    }

    public function __construct()
    {
        $this->productOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateOrder(): ?\DateTimeInterface
    {
        return $this->date_order;
    }

    public function setDateOrder(\DateTimeInterface $date_order): static
    {
        $this->date_order = $date_order;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): Order
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getProductOrders(): Collection
    {
        return $this->productOrders;
    }

    public function setProductOrders(Collection $productOrders): Order
    {
        $this->productOrders = $productOrders;

        return $this;
    }

    public function removeProductOrder(ProductOrder $productOrder)
    {
        $this->productOrders->removeElement($productOrder);
        $productOrder->setOrder(null);
        return $this;
    }
}
