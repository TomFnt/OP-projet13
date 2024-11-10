<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Services\ProductOrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderController extends AbstractController
{
    #[Route('/validate/order', name: 'validate_order')]
    public function validateOrder(ProductOrderService $productOrderService, UserInterface $user ,EntityManagerInterface $em): Response
    {
        $order = $productOrderService->checkIfTempOderExists($user);

        if (!$order) {
            $this->addFlash('error', 'Aucune commande en attente trouvée.');
            return $this->redirectToRoute('cart_index');
        }

        $dateOrdered = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $order->setStatus('ordered');
        $order->setOrderNum($dateOrdered);
        $order->setDateOrder($dateOrdered);
        $em->persist($order);
        $em->flush();


        return $this->redirectToRoute('app_home');
    }
}
