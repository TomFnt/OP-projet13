<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\ProductOrder;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductOrderController extends AbstractController
{
    #[Route('/addtocart/{id}', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(Product $product, Request $request, EntityManagerInterface $em, UserInterface $user = null): Response
    {

        $quantity = $request->request->get('quantity');

        // wip check user, add them in isgranted
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // check if an order exist with pending status for this user
        $order = $em->getRepository(Order::class)->findOneBy([
            'user' => $user,
            'status' => 'pending',
        ]);

        // create them if doesn't exist
        if (!$order) {
            $order = new Order();
            $order->setUser($user);
            $order->setStatus('pending');
            $order->setTotalPrice(0); // to do : add calculateTotalPrice()
            $em->persist($order);
            $em->flush();
        }

        // WIP: case create new productOrder
        $productOrder = new ProductOrder();
        $productOrder->setProduct($product);
        $productOrder->setOrder($order);
        $productOrder->setQuantity($quantity);

        $em->persist($productOrder);
        $em->flush();

        // Redirect in cart route after modification / creation
        return $this->redirectToRoute('cart_index', ['id' => $productOrder->getId()]);
    }

    #[Route('/cart', name: 'cart_index')]
    public function cartIndex(OrderRepository $orderRepository, UserInterface $user): Response
    {

        $user = $this->getUser();

        // check if an order exist with pending status for this user
        $cart = $orderRepository->findOneBy([
            'user' => $user,
            'status' => 'pending',
        ]);


        if (!$cart) {
            return $this->render('cart/cart_empty.html.twig', [
                'cart' => null
            ]);
        }


        return $this->render('cart/cart.html.twig', [
            'cart' => $cart,
        ]);
    }

}
