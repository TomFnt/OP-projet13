<?php

namespace App\Services;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\ProductOrder;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\ProductOrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductOrderService
{
    public function __construct(EntityManagerInterface $em, OrderRepository $orderRepository, ProductOrderRepository $productOrderRepository)
    {
        $this->em = $em;
        $this->orderRepository = $orderRepository;
        $this->productOrderRepository = $productOrderRepository;
    }

    public function addToCart(Product $product, int $quantity, Order $order)
    {
        $productOrder = $this->checkIfProductAlreadyAddInCart($product, $order);

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
    public function removeToCart(Product $product, Order $order){
        $productOrder = $this->checkIfProductAlreadyAddInCart($product, $order);

        if($productOrder == false ){
            return ;
        }
        $order->removeProductOrder($productOrder);
        $this->em->flush();
    }

    public function clearAllProductInCart(Order $order)
    {
        if ($order->getProductOrders()->isEmpty()) {
            return;
        }

        foreach ($order->getProductOrders() as $productOrder) {
            $order->removeProductOrder($productOrder);
        }

        $this->em->flush();
    }

    public function checkIfTempOderExists(User $user)
    {
        $totalPrice = 0.0;
         $order = $this->orderRepository->findOneBy([
            'user' => $user,
            'status' => 'pending',
        ]);

         /*this first condition calculate totalPrice  and return it in $totalPrice each time an productOrder object are found during call of function checkIfTempOrderExists.*/
         if($order != null){
            $totalPrice = $this->calculateTotalPrice($order);
         }

         /*Update value of Order totalPrice if $totalPrice variable aren't equal to actual Order totalPrice */
         if($order !== null && $order->getTotalPrice() != $totalPrice){
            $order->setTotalPrice($totalPrice);
            $this->em->persist($order);
            $this->em->flush();
         }

         return $order;
    }

    /**
     * @param Product $product
     * @param Order $order
     * @return $1|false|mixed
     */
    public function checkIfProductAlreadyAddInCart(Product $product, Order $order)
    {
        return $order->getProductOrders()->filter(fn (ProductOrder $productOrder) => $productOrder->getProduct() === $product)->first();

    }

    public function createTempOrder(User $user)
    {
        $order = new Order();
        $order->setUser($user);
        $order->setStatus('pending');
        $order->setTotalPrice(0); // to do : add calculateTotalPrice()
        $this->em->persist($order);
        $this->em->flush();
    }

    /* Check product quantity if this product are in cart */
    public function getQuantityProductInCart(User $user, Product $product)
    {
        $quantity = null;
        $cart = $this->orderRepository->findOneBy(['user' => $user, 'status' => 'pending']);

        if (null != $cart) {
            foreach ($cart->getProductOrders() as $productOrder) {
                if ($productOrder->getProduct()->getId() === $product->getId()) {
                    $quantity = $productOrder->getQuantity();
                    break;
                }
            }
        }

        return $quantity;
    }

    public function calculateTotalPrice(Order $order): float
    {
        $totalPrice = 0.0;

        if( $order->getProductOrders()->count() > 0){
            foreach ($order->getProductOrders() as $productOrder) {
                $productPrice = $productOrder->getProduct()->getPrice();
                $quantity = $productOrder->getQuantity();

                $totalPrice += $productPrice * $quantity;
            }
        }
        return $totalPrice;
    }
}
