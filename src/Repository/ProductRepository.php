<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use App\Service\ExternalApiService;

class ProductRepository implements ProductRepositoryInterface
{
    private array $products = [];
    private ExternalApiService $externalApiService;

    public function __construct(ExternalApiService $externalApiService)
    {
        $this->externalApiService = $externalApiService;
        $this->products = $this->fetchProducts();
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getProductById(int $id): ?Product
    {
        return $this->products[$id] ?? null;
    }

    private function fetchProducts(): array
    {
        $data = $this->externalApiService->fetchProducts();
        $products = [];

        foreach ($data as $item) {
        }

        return $products;
    }
}
