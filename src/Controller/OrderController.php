<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Http\MyJsonResponse;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/orders')]
final class OrderController extends AbstractController
{
    public function __construct(private OrderRepository $orderRepository)
    {
    }

    #[
        Route(
            name: '/',
            requirements: [],
            methods: ['GET'],
        )
    ]
    public function getOrdersAction(Request $request): MyJsonResponse
    {
        $newOrder = new Order();
        $newOrder->setUserId('test-user-id');
        $newOrder = $this->orderRepository->saveOrder($newOrder);
        $orders = $this->orderRepository->getOrders('test-user-id');
        return new MyJsonResponse($newOrder);
    }
}
