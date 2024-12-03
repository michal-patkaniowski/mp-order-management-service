<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Order;
use App\Repository\OrderRepositoryInterface;
use InvalidArgumentException;

class OrderService implements OrderServiceInterface
{
    public function __construct(private readonly OrderRepositoryInterface $orderRepository)
    {
    }
    public function setActiveUserOrder(string $userId, Order $order): void
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

    public function getActiveUserOrder(string $userId): ?Order
    {
        $activeOrders = $this->orderRepository->findActiveUserOrders($userId);
        if (count($activeOrders) === 0) {
            return null;
        }

        return $activeOrders[0];
    }

    public function createNewUserOrder(string $userId): Order
    {
        $order = new Order();
        $order->setUserId($userId);
        $this->orderRepository->saveOrder($order);

        return $order;
    }

    public function updateOrderStatus(Order $order, bool $attemptedActionIsCancel): void
    {
        $order->setActive(!$attemptedActionIsCancel);
        $this->orderRepository->saveOrder($order);
    }
}
