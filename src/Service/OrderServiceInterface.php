<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Order;

interface OrderServiceInterface
{
    public function setActiveUserOrder(string $userId, Order $order): void;

    public function getActiveUserOrder(string $userId): ?Order;

    public function createNewUserOrder(string $userId): Order;

    public function updateOrderStatus(Order $order, bool $attemptedActionIsCancel): void;
}
