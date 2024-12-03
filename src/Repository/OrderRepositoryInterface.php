<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;

interface OrderRepositoryInterface
{
    /**
     * @return Order[]
     */
    public function getUserOrders(string $userId): array;

    public function getOrderById(int $id): Order;

    public function saveOrder(Order $order): Order;

    /**
     * @return Order[]
     */
    public function findActiveUserOrders(string $userId): array;
}
