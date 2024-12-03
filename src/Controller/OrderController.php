<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\JsonResponseFromObject;
use App\Repository\OrderRepositoryInterface;
use App\Repository\ProductRepositoryInterface;
use App\Service\OrderServiceInterface;
use App\Utils\ApiDataGuardInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[
    Route('/orders'),
    OA\Info(
        title: "Order API",
        version: "1.0.0",
        description: "API for managing orders"
    )
]
final class OrderController extends AbstractController
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ProductRepositoryInterface $productRepository,
        private OrderServiceInterface $orderService,
        private ApiDataGuardInterface $apiDataGuard
    ) {
    }

    #[OA\Get(
        path: "/orders",
        summary: "Get all user's orders",
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful response",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Order"))
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
    public function getAllUserOrdersAction(Request $request): JsonResponseFromObject
    {
        $orders = $this->orderRepository->getUserOrders('test-user-id');
        return new JsonResponseFromObject($orders);
    }

    #[OA\Get(
        path: "/orders/{orderId}",
        summary: "Get order by ID",
        parameters: [
            new OA\Parameter(
                name: "orderId",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful response",
                content: new OA\JsonContent(ref: "#/components/schemas/Order")
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden"
            ),
            new OA\Response(
                response: 404,
                description: "Order not found"
            )
        ]
    )]
    #[
        Route(
            path: '/{orderId}',
            requirements: ['orderId' => '\d+'],
            methods: ['GET'],
        )
    ]
    public function getOrderAction(Request $request, int $orderId): JsonResponseFromObject
    {
        $order = $this->orderRepository->getOrderById($orderId);
        $this->apiDataGuard->ensureOrderAccess($order);

        return new JsonResponseFromObject($order);
    }

    #[OA\Get(
        path: "/orders/active",
        summary: "Get active user order",
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful response",
                content: new OA\JsonContent(ref: "#/components/schemas/Order")
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden"
            )
        ]
    )]
    #[
        Route(
            path: '/active',
            requirements: [],
            methods: ['GET'],
        )
    ]
    public function getActiveUserOrderAction(Request $request): JsonResponseFromObject
    {
        $order = $this->orderService->getActiveUserOrder('test-user-id');

        return new JsonResponseFromObject($order);
    }

    #[OA\Post(
        path: "/orders",
        summary: "Create new active user order",
        responses: [
            new OA\Response(
                response: 201,
                description: "Order created",
                content: new OA\JsonContent(ref: "#/components/schemas/Order")
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
            methods: ['POST'],
        )
    ]
    public function createNewActiveUserOrderAction(Request $request): JsonResponseFromObject
    {
        $newOrder = $this->orderService->createNewUserOrder('test-user-id');
        $this->orderService->setActiveUserOrder('test-user-id', $newOrder);
        return new JsonResponseFromObject($newOrder);
    }

    #[OA\Post(
        path: "/orders/{statusAction}/{orderId}",
        summary: "Change order status",
        parameters: [
            new OA\Parameter(
                name: "orderId",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "statusAction",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", enum: ["cancel", "restore"])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful response",
                content: new OA\JsonContent(ref: "#/components/schemas/Order")
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden"
            ),
            new OA\Response(
                response: 404,
                description: "Order not found"
            )
        ]
    )]
    #[
        Route(
            path: '/{statusAction}/{orderId}',
            requirements: [
            'orderId' => '\d+',
            'statusAction' => 'cancel|restore'
            ],
            methods: ['POST'],
        )
    ]
    public function changeOrderStatusAction(
        Request $request,
        int $orderId,
        string $statusAction
    ): JsonResponseFromObject {
        $order = $this->orderRepository->getOrderById($orderId);
        $attemptedActionIsCancel = $statusAction === 'cancel';

        $this->apiDataGuard->ensureOrderAccess($order);
        $this->apiDataGuard->checkOrderStatus($order, $attemptedActionIsCancel);

        $this->orderService->updateOrderStatus($order, $attemptedActionIsCancel);
        return new JsonResponseFromObject($order);
    }

    #[OA\Post(
        path: "/orders/{orderId}/products/{productId}",
        summary: "Add product to order",
        parameters: [
            new OA\Parameter(
                name: "orderId",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
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
                content: new OA\JsonContent(ref: "#/components/schemas/Order")
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden"
            ),
            new OA\Response(
                response: 404,
                description: "Order or product not found"
            )
        ]
    )]
    #[
        Route(
            path: '/{orderId}/products/{productId}',
            requirements: [
            'orderId' => '\d+',
            'productId' => '\d+'
            ],
            methods: ['POST'],
        )
    ]
    public function addProductToOrderAction(Request $request, int $orderId, int $productId): JsonResponseFromObject
    {
        $order = $this->orderRepository->getOrderById($orderId);

        $this->apiDataGuard->ensureOrderAccess($order);
        $this->apiDataGuard->ensureOrderIsActive($order);

        $product = $this->productRepository->getProductById($productId);
        $this->apiDataGuard->ensureProductExists($product);
        $this->apiDataGuard->ensureProductIsAvailable($product);

        $order->addProductToOrder($product, 1);
        $this->orderRepository->saveOrder($order);

        return new JsonResponseFromObject($order);
    }

    #[OA\Delete(
        path: "/orders/{orderId}/products/{productId}",
        summary: "Remove product from order",
        parameters: [
            new OA\Parameter(
                name: "orderId",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
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
                content: new OA\JsonContent(ref: "#/components/schemas/Order")
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden"
            ),
            new OA\Response(
                response: 404,
                description: "Order or product not found"
            )
        ]
    )]
    #[
        Route(
            path: '/{orderId}/products/{productId}',
            requirements: [
            'orderId' => '\d+',
            'productId' => '\d+'
            ],
            methods: ['DELETE'],
        )
    ]
    public function removeProductFromOrderAction(Request $request, int $orderId, int $productId): JsonResponseFromObject
    {
        $order = $this->orderRepository->getOrderById($orderId);

        $this->apiDataGuard->ensureOrderAccess($order);
        $this->apiDataGuard->ensureOrderIsActive($order);

        $product = $this->productRepository->getProductById($productId);

        $this->apiDataGuard->ensureProductInOrder($order, $product);

        $order->removeProductFromOrder($product);

        $this->orderRepository->saveOrder($order);
        return new JsonResponseFromObject($order);
    }
}
