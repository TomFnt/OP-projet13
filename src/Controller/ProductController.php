<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Services\ProductOrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/product/{id}', name: 'product_index', requirements: ['id' => '\d+'])]
    public function productIndex(Product $product, ProductOrderService $productOrderService, int $id): Response
    {
        $user = $this->getUser();

        $defaultQuantity = $productOrderService->getQuantityProductInCart($user, $product);
        $maxQuantity = $product->getStockQuantity();
        $minQuantity = 0; /* it's 0 in case product are set with quantity in user cart */

        /* set default quantity and min quantity to 1 if product aren't in cart */
        if (null == $defaultQuantity) {
            $defaultQuantity = 1;
            $minQuantity = 1;
        }

        $form = $this->createForm(ProductType::class,
            ['defaultQuantity' => $defaultQuantity,
                'minQuantity' => $minQuantity,
                'maxQuantity' => $maxQuantity,
            ]);

        return $this->render('products/product.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }
}
