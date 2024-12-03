<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\MyJsonResponse;
use App\Repository\OrderRepositoryInterface;
use App\Repository\ProductRepositoryInterface;
use App\Service\OrderServiceInterface;
use App\Service\ApiDataGuardInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/orders')]
final class OrderController extends AbstractController
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ProductRepositoryInterface $productRepository,
        private OrderServiceInterface $orderService,
        private ApiDataGuardInterface $apiDataGuard
    ) {
    }

    #[
        Route(
            name: '/all',
            requirements: [],
            methods: ['GET'],
        )
    ]
    public function getAllUserOrdersAction(Request $request): MyJsonResponse
    {
        $orders = $this->orderRepository->getUserOrders('test-user-id');
        return new MyJsonResponse($orders);
    }

    #[
        Route(
            name: '/{orderId}',
            requirements: ['orderId' => '\d+'],
            methods: ['GET'],
        )
    ]
    public function getOrderAction(Request $request, int $orderId): MyJsonResponse
    {
        $order = $this->orderRepository->getOrderById($orderId);
        $this->apiDataGuard->ensureOrderAccess($order);

        return new MyJsonResponse($order);
    }

    #[
        Route(
            name: '/',
            requirements: [],
            methods: ['GET'],
        )
    ]
    public function getActiveUserOrderAction(Request $request): MyJsonResponse
    {
        $order = $this->orderService->getActiveUserOrder('test-user-id');

        return new MyJsonResponse($order);
    }

    #[
        Route(
            name: '/',
            requirements: [],
            methods: ['POST'],
        )
    ]
    public function createNewActiveUserOrderAction(Request $request): MyJsonResponse
    {
        $newOrder = $this->orderService->createNewUserOrder('test-user-id');
        $this->orderService->setActiveUserOrder('test-user-id', $newOrder);
        return new MyJsonResponse($newOrder);
    }

    #[
        Route(
            name: '/{statusAction}/{orderId}',
            requirements: [
            'orderId' => '\d+',
            'statusAction' => 'cancel|restore'
            ],
            methods: ['POST'],
        )
    ]
    public function changeOrderStatusAction(Request $request, int $orderId, string $statusAction): MyJsonResponse
    {
        $order = $this->orderRepository->getOrderById($orderId);
        $attemptedActionIsCancel = $statusAction === 'cancel';

        $this->apiDataGuard->ensureOrderAccess($order);
        $this->apiDataGuard->checkOrderStatus($order, $attemptedActionIsCancel);

        $this->orderService->updateOrderStatus($order, $attemptedActionIsCancel);
        return new MyJsonResponse($order);
    }

    #[
        Route(
            name: '/{orderId}/products/{productId}',
            requirements: [
            'orderId' => '\d+',
            'productId' => '\d+'
            ],
            methods: ['POST'],
        )
    ]
    public function addProductToOrderAction(Request $request, int $orderId, int $productId): MyJsonResponse
    {
        $order = $this->orderRepository->getOrderById($orderId);

        $this->apiDataGuard->ensureOrderAccess($order);
        $this->apiDataGuard->ensureOrderIsActive($order);

        $product = $this->productRepository->getProductById($productId);
        $this->apiDataGuard->ensureProductExists($product);
        $this->apiDataGuard->ensureProductIsAvailable($product);

        $order->addProductToOrder($product, 1);
        $this->orderRepository->saveOrder($order);

        return new MyJsonResponse($order);
    }

    #[
        Route(
            name: '/{orderId}/products/{productId}',
            requirements: [
            'orderId' => '\d+',
            'productId' => '\d+'
            ],
            methods: ['DELETE'],
        )
    ]
    public function removeProductFromOrderAction(Request $request, int $orderId, int $productId): MyJsonResponse
    {
        $order = $this->orderRepository->getOrderById($orderId);

        $this->apiDataGuard->ensureOrderAccess($order);
        $this->apiDataGuard->ensureOrderIsActive($order);

        $product = $this->productRepository->getProductById($productId);

        $this->apiDataGuard->ensureProductInOrder($order, $product);

        $order->removeProductFromOrder($product);

        $this->orderRepository->saveOrder($order);
        return new MyJsonResponse($order);
    }
}
