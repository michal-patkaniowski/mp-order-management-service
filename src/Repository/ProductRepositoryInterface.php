<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;

interface ProductRepositoryInterface
{
    /**
     * @return Product[]
     */
    public function getProducts(): array;

    public function getProductById(int $id): ?Product;
}
