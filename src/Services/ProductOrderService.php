<?php

namespace App\Services;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\ProductOrder;
use App\Entity\User;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;


class ProductOrderService
{
    public function __construct(EntityManagerInterface $em, OrderRepository $orderRepository) {
        $this->em = $em;
        $this->orderRepository = $orderRepository;
    }


    public function addToCart(Product $product, int $quantity, Order $order){
        $productOrder = new ProductOrder();
        $productOrder->setProduct($product);
        $productOrder->setOrder($order);
        $productOrder->setQuantity($quantity);

        $this->em->persist($productOrder);
        $this->em->flush();
    }

    public function checkIfTempOderExists(User $user){

       return $order = $this->orderRepository->findOneBy([
            'user' => $user,
            'status' => 'pending',
        ]);
    }

    public function createTempOrder(User $user){
        $order = new Order();
        $order->setUser($user);
        $order->setStatus('pending');
        $order->setTotalPrice(0); // to do : add calculateTotalPrice()
        $this->em->persist($order);
        $this->em->flush();
    }
}