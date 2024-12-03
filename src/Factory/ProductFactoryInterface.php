<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Product;

interface ProductFactoryInterface
{
    public function createFromArray(array $data): Product;
}
