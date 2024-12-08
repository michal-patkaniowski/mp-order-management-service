<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\Order;
use App\Entity\Product;

interface ApiProductGuardInterface
{
    public function ensureProductExists(?Product $product): void;
    public function ensureProductIsAvailable(Product $product): void;
    public function ensureProductInOrder(Order $order, Product $product): void;
}
