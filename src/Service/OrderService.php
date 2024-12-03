<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Order;
use App\Repository\OrderRepositoryInterface;
use InvalidArgumentException;

class OrderService implements OrderServiceInterface
{
    private function __construct(private readonly OrderRepositoryInterface $orderRepository)
    {
    }
    public function setCurrentUserOrder(string $userId, Order $order): void
    {
        if ($order->getUserId() !== $userId) {
            throw new InvalidArgumentException('The order user does not match the provided user id');
        }

        $activeOrders = $this->orderRepository->findActiveUserOrders($userId);
        foreach ($activeOrders as $activeOrder) {
            $activeOrder->setActive(false);
            $this->orderRepository->saveOrder($activeOrder);
        }

        //activate the new order
        $order->setActive(true);
        $this->orderRepository->saveOrder($order);
    }

    public function getCurrentUserOrder(string $userId): Order
    {
        $activeOrders = $this->orderRepository->findActiveUserOrders($userId);
        if (count($activeOrders) === 0) {
            throw new InvalidArgumentException('No active order found for the provided user id');
        }

        return $activeOrders[0];
    }
}
