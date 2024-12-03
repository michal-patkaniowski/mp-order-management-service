<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Http\MyJsonResponse;
use App\Repository\OrderRepositoryInterface;
use App\Service\OrderServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Exception;

#[Route('/orders')]
final class OrderController extends AbstractController
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderServiceInterface $orderService
    ) {
    }

    #[
        Route(
        name: '/',
        requirements: [],
        methods: ['GET'],
    )
    ]
    public function getOrdersAction(Request $request): MyJsonResponse
    {
        $orders = $this->orderRepository->getUserOrders('test-user-id');
        return new MyJsonResponse($orders);
    }

    #[
        Route(
        name: '/{orderId}',
        requirements: ['orderId' => Requirement::DIGITS],
        methods: ['GET'],
    )
    ]
    public function getOrderAction(Request $request, int $orderId): MyJsonResponse
    {
        $order = $this->orderRepository->getOrderById($orderId);
        $this->ensureOrderAccess($order);

        return new MyJsonResponse($order);
    }

    #[
        Route(
        name: '/',
        requirements: [],
        methods: ['POST'],
    )
    ]
    public function createNewUserOrderAction(Request $request): MyJsonResponse
    {
        $newOrder = new Order();
        $newOrder->setUserId('test-user-id');
        $this->orderService->setCurrentUserOrder('test-user-id', $newOrder);
        $newOrder = $this->orderRepository->saveOrder($newOrder);
        return new MyJsonResponse($newOrder);
    }

    #[
        Route(
        name: '/{statusAction}/{orderId}',
        requirements: [
            'orderId' => Requirement::DIGITS,
            'statusAction' => 'cancel|restore'
        ],
        methods: ['POST'],
    )
    ]
    public function changeOrderStatusAction(Request $request, int $orderId, string $statusAction): MyJsonResponse
    {
        $order = $this->orderRepository->getOrderById($orderId);
        $cancel = $statusAction === 'cancel';
        $this->ensureOrderAccess($order);
        if (!$order->isActive() && $cancel) {
            return new MyJsonResponse('Order is already inactive', 400);
        }
        if ($order->isActive() && !$cancel) {
            return new MyJsonResponse('Order is already active', 400);
        }

        $order->setActive(!$cancel);
        $this->orderRepository->saveOrder($order);
        return new MyJsonResponse($order);
    }

    #[
        Route(
        name: '/{orderId}/products/{productId}',
        requirements: [
            'orderId' => Requirement::DIGITS,
            'productId' => Requirement::DIGITS
        ],
        methods: ['POST'],
    )
    ]
    public function addProductToOrderAction(Request $request, int $orderId, int $productId): MyJsonResponse
    {
        $order = $this->orderRepository->getOrderById($orderId);
        $this->ensureOrderAccess($order);
        $this->ensureOrderIsActive($order);
        $product = $this->productRepository->getProductById($productId);
        $this->ensureProductAvailability($product);

        $order->addProductToOrder($product, 1);
        $this->orderRepository->saveOrder($order);

        return new MyJsonResponse($order);
    }

    #[
        Route(
        name: '/{orderId}/products/{productId}',
        requirements: [
            'orderId' => Requirement::DIGITS,
            'productId' => Requirement::DIGITS
        ],
        methods: ['DELETE'],
    )
    ]
    public function removeProductFromOrderAction(Request $request, int $orderId, int $productId): MyJsonResponse
    {
        $order = $this->orderRepository->getOrderById($orderId);
        $this->ensureOrderAccess($order);
        $this->ensureOrderIsActive($order);
        $product = $this->productRepository->getProductById($productId);
        $this->ensureProductInOrder($order, $product);

        $order->removeProductFromOrder($product);

        $this->orderRepository->saveOrder($order);
        return new MyJsonResponse($order);
    }

    private function ensureProductAvailability(Product $product): void
    {
        if ($product === null) {
            throw new Exception('Product not found', 404);
        }
        if (!$product->isActive()) {
            throw new Exception('Product is not active', 400);
        }
    }

    private function ensureProductInOrder(Order $order, Product $product): void
    {
        $orderProduct = $order->getOrderProducts()->filter(
            fn(OrderProduct $orderProduct) => $orderProduct->getProduct()->getId() === $product->getId()
        )->first();
        if ($orderProduct === false) {
            throw new Exception('Product not found in the order', 404);
        }
    }

    private function ensureOrderAccess(?Order $order): void
    {
        if ($order === null || $order->getUserId() !== 'test-user-id') {
            throw new Exception('Access forbidden', 403);
        }
    }

    private function ensureOrderIsActive(Order $order): void
    {
        if (!$order->isActive()) {
            throw new Exception('Order is not active', 400);
        }
    }
}
