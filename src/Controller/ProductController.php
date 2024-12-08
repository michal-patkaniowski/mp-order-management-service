<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\JsonResponseFromObject;
use App\Repository\ProductRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[
    Route('/products'),
    OA\Info(
        title: "Product API",
        version: "1.0.0",
        description: "API for managing products"
    )
]
final class ProductController extends AbstractController
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    #[OA\Get(
        path: "/products",
        summary: "Get all products",
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful response",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Product"))
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden"
            )
        ]
    )]
    #[
        Route(
            path: '',
            requirements: [],
            methods: ['GET'],
        )
    ]
    public function getAllProductsAction(): JsonResponseFromObject
    {
        $products = $this->productRepository->getProducts();
        return new JsonResponseFromObject($products);
    }

    #[OA\Get(
        path: "/products/{productId}",
        summary: "Get product by ID",
        parameters: [
            new OA\Parameter(
                name: "productId",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful response",
                content: new OA\JsonContent(ref: "#/components/schemas/Product")
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden"
            ),
            new OA\Response(
                response: 404,
                description: "Product not found"
            )
        ]
    )]
    #[
        Route(
            path: '/{productId}',
            requirements: ['productId' => '\d+'],
            methods: ['GET'],
        )
    ]
    public function getProductAction(int $productId): JsonResponseFromObject
    {
        $product = $this->productRepository->getProductById($productId);
        return new JsonResponseFromObject($product);
    }
}
