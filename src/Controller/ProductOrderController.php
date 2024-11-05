<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\ProductOrder;
use App\Form\ProductType;
use App\Services\ProductOrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductOrderController extends AbstractController
{
    #[Route('/addtocart/{id}', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(Product $product, Request $request, ProductOrderService $productOrderService, EntityManagerInterface $em, ?UserInterface $user = null): Response
    {
        $form = $this->createForm(ProductType::class,
            ['defaultQuantity' => 1,
                'minQuantity' => 0,
                'maxQuantity' => 10,
            ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quantity = $form->get('quantity')->getData();

            // wip check user, add them in isgranted
            if (!$user) {
                return $this->redirectToRoute('app_login');
            }

            // check if an order exist with pending status for this user
            $order = $productOrderService->checkIfTempOderExists($user);

            // create them if doesn't exist
            if (!$order) {
                $productOrderService->addToCart($user);
            }

            if (0 == $quantity) {
                $productOrderService->removeToCart($product, $order);
            } else {
                $productOrderService->addToCart($product, $quantity, $order);
            }
        } else {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout au panier.');
        }

        // Redirect in cart route after modification / creation
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart', name: 'cart_index')]
    public function cartIndex(ProductOrderService $productOrderService, UserInterface $user): Response
    {
        $user = $this->getUser();

        // check if an order exist with pending status for this user
        $cart = $productOrderService->checkIfTempOderExists($user);

        return $this->render('cart/cart.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/cart/clear', name: 'cart_clear_all')]
    public function removeAllProductFromCart( ProductOrderService $productOrderService, UserInterface $user): Response
    {
        if(!$user) {
        return $this->redirectToRoute('app_login');
    }
        $order = $productOrderService->checkIfTempOderExists($user);

        if (!$order) {
            $this->addFlash('info', 'Votre panier est déjà vide.');
            return $this->redirectToRoute('cart_index');
        }

        $productOrderService->clearAllProductInCart($order);

        $this->addFlash('success', 'Votre panier a été vider avec succés. .');
        return $this->redirectToRoute('cart_index');
    }
}
