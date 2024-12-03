<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Order;

interface OrderServiceInterface
{
    public function setCurrentUserOrder(string $userId, Order $order): void;

    public function getCurrentUserOrder(string $userId): Order;
}
