<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\Order;

interface ApiOrderGuardInterface
{
    public function ensureOrderExists(?Order $order): void;
    public function ensureOrderAccess(?Order $order, string $userId): void;
    public function ensureOrderIsActive(Order $order): void;
    public function checkOrderStatus(Order $order, bool $attemptedActionIsCancel): void;
}
