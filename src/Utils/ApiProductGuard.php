<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use Exception;

class ApiProductGuard implements ApiProductGuardInterface
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
            fn(OrderProduct $orderProduct) => $orderProduct->getProduct()->getId() === $product->getId()
        )->first();
        if ($orderProduct === false) {
            throw new Exception('Product not found in the order', 404);
        }
    }
}
