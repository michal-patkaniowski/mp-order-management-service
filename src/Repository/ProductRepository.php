<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use App\Factory\ProductFactoryInterface;
use App\Service\ExternalApiServiceInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private ExternalApiServiceInterface $externalApiService,
        private ParameterBagInterface $params,
        private ProductFactoryInterface $productFactory
    ) {
    }

    /**
     * @return Product[]
     */
    public function getProducts(): array
    {
        $url = $this->params->get('FAKESTORE_API_URL') . '/' . $this->params->get('FAKESTORE_API_ENDPOINT_PRODUCTS');
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
            $this->params->get('FAKESTORE_API_URL') . '/' .
            $this->params->get('FAKESTORE_API_ENDPOINT_PRODUCTS') . '/' . $id;
        $data = $this->externalApiService->fetchData($url);

        return $this->productFactory->createFromArray($data);
    }
}
