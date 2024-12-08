<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\Order;
use Exception;

class ApiOrderGuard implements ApiOrderGuardInterface
{
    public function ensureOrderExists(?Order $order): void
    {
        if ($order === null) {
            throw new Exception('Order not found', 404);
        }
    }

    public function ensureOrderAccess(?Order $order, string $userId): void
    {
        if ($order->getUserId() !== $userId) {
            throw new Exception('Access forbidden', 403);
        }
    }

    public function ensureOrderIsActive(Order $order): void
    {
        if (!$order->isActive()) {
            throw new Exception('Order is not active', 400);
        }
    }

    public function checkOrderStatus(Order $order, bool $attemptedActionIsCancel): void
    {
        if (!$order->isActive() && $attemptedActionIsCancel) {
            throw new Exception('Order is already inactive', 400);
        }
        if ($order->isActive() && !$attemptedActionIsCancel) {
            throw new Exception('Order is already active', 400);
        }
    }
}
