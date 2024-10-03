<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus at tortor libero. Nam ac lobortis ligula. Vestibulum aliquam fermentum libero, eu ornare dui sollicitudin quis. Phasellus non velit nulla. Pellentesque rhoncus nunc et lectus imperdiet sagittis. Nunc semper ex diam, non vestibulum lacus faucibus in. ';

        $product1 = new Product();

        $product1->setName("Kit d'hygiène recyclable");
        $product1->setShortDescription('Pour une salle de bain éco-friendly');
        $product1->setFullDescription($lorem);
        $product1->setPrice(24.99);
        $product1->setPicture('product1.jpg');
        $product1->setStockQuantity(10);
        $product1->setNbSell(0);
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName('Kit shot tropical');
        $product2->setShortDescription('Fruits frais, pressés à froid');
        $product2->setFullDescription($lorem);
        $product2->setPrice(4.50);
        $product2->setPicture('product2.jpg');
        $product2->setStockQuantity(10);
        $product2->setNbSell(0);
        $manager->persist($product2);

        $product3 = new Product();
        $product3->setName('Gourde en bois');
        $product3->setShortDescription("50cl, bois d'olivier");
        $product3->setFullDescription($lorem);
        $product3->setPrice(4.50);
        $product3->setPicture('product3.jpg');
        $product3->setStockQuantity(10);
        $product3->setNbSell(0);
        $manager->persist($product3);

        $manager->flush();
    }
}
