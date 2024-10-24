<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/product/{id}', name: 'product_index', requirements: ['id' => '\d+'])]
    public function productIndex(Product $product, int $id): Response
    {

        return $this->render('products/product.html.twig', [
            'product' => $product,
        ]);
    }
}
