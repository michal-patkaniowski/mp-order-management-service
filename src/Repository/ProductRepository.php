<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use App\Factory\ProductFactoryInterface;
use App\Service\ExternalApiServiceInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private ExternalApiServiceInterface $externalApiService,
        private ProductFactoryInterface $productFactory
    ) {
    }

    /**
     * @return Product[]
     */
    public function getProducts(): array
    {
        $url = getenv('FAKESTORE_API_URL') . '/' . getenv('FAKESTORE_API_ENDPOINT_PRODUCTS');
        $data = $this->externalApiService->fetchData($url);
        $products = [];

        foreach ($data as $item) {
            $products[] = $this->productFactory->createFromArray($item);
        }

        return $products;
    }

    public function getProductById(int $id): ?Product
    {
        $url =
            getenv('FAKESTORE_API_URL') . '/' .
            getenv('FAKESTORE_API_ENDPOINT_PRODUCTS') . '/' . $id;
        $data = $this->externalApiService->fetchData($url);

        return $this->productFactory->createFromArray($data);
    }
}
