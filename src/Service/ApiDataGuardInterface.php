<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;

interface ApiDataGuardInterface
{
    public function ensureProductExists(?Product $product): void;
    public function ensureProductIsAvailable(Product $product): void;
    public function ensureProductInOrder(Order $order, Product $product): void;
    public function ensureOrderAccess(?Order $order): void;
    public function ensureOrderIsActive(Order $order): void;
    public function checkOrderStatus(Order $order, bool $attemptedActionIsCancel): void;
}
