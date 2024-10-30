<?php

namespace App\Services;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\ProductOrder;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\ProductOrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;


class ProductOrderService
{
    public function __construct(EntityManagerInterface $em, OrderRepository $orderRepository, ProductOrderRepository $productOrderRepository) {
        $this->em = $em;
        $this->orderRepository = $orderRepository;
        $this->productOrderRepository = $productOrderRepository;
    }


    public function addToCart(Product $product, int $quantity, Order $order){

        $productOrder = $this->productOrderRepository->findOneBy([
            'order' => $order,
            'product' => $product
        ]);

        if ($productOrder) {
            $productOrder->setQuantity($quantity);
        } else {
            $productOrder = new ProductOrder();
            $productOrder->setProduct($product);
            $productOrder->setOrder($order);
            $productOrder->setQuantity($quantity);
        }
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

    /*Check product quantity if this product are in cart */
    public function getQuantityProductInCart(User $user, Product $product){

        $quantity = null;
        $cart = $this->orderRepository->findOneBy(['user' => $user, 'status' => 'pending']);

        if ($cart != null) {
            foreach ($cart->getProductOrders() as $productOrder) {
                if ($productOrder->getProduct()->getId() === $product->getId()) {
                   $quantity = $productOrder->getQuantity();
                    break;
                }
            }
        }


        return $quantity;
    }
}