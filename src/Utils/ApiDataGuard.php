<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use Exception;

class ApiDataGuard implements ApiDataGuardInterface
{
    public function ensureProductExists(?Product $product): void
    {
        if ($product === null) {
            throw new Exception('Product not found', 404);
        }
    }

    public function ensureProductIsAvailable(Product $product): void
    {
        if (!$product->isAvailable()) {
            throw new Exception('Product is not active', 400);
        }
    }

    public function ensureProductInOrder(Order $order, Product $product): void
    {
        $orderProduct = $order->getOrderProducts()->filter(
            fn(OrderProduct $orderProduct) => $orderFProduct->getProduct()->getId() === $product->getId()
        )->first();
        if ($orderProduct === false) {
            throw new Exception('Product not found in the order', 404);
        }
    }

    public function ensureOrderAccess(?Order $order, string $userId): void
    {
        if ($order === null || $order->getUserId() !== $userId) {
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
