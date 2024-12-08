<?php

declare(strict_types=1);

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrdersControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCreateOrder(): array
    {
        $this->client->request('POST', '/orders');
        $this->assertResponseIsSuccessful('Failed to create order');
        $order = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(true, $order['active'], 'The new order is not active');

        print_r('Order created: ');
        print_r($order);

        return $order;
    }

    /**
     * @depends testCreateOrder
     */
    public function testGetOrderById(array $order): void
    {
        $this->client->request(
            'GET',
            '/orders/' . $order['id'],
            [],
            [],
            ['HTTP_Authorization' => 'Bearer ' . $order['userId']]
        );
        $this->assertResponseIsSuccessful('Failed to retrieve order by ID');
        $retrievedOrder = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(
            $order['id'],
            $retrievedOrder['id'],
            'The order retrieved by ID does not match the created order'
        );
        $this->assertEquals(
            true,
            $retrievedOrder['active'],
            'The order retrieved by ID is not active'
        );
        $this->assertEquals(
            $order['userId'],
            $retrievedOrder['userId'],
            'The order retrieved by ID does not belong to the user'
        );
    }

    /**
     * @depends testCreateOrder
     */
    public function testGetActiveOrder(array $order): void
    {
        $this->client->request(
            'GET',
            '/orders/active',
            [],
            [],
            ['HTTP_Authorization' => 'Bearer ' . $order['userId']]
        );
        $this->assertResponseIsSuccessful('Failed to retrieve active order');
        $retrievedOrder = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertOrderEquals(
            $order,
            $retrievedOrder,
            'The retrieved user\'s active order does not match the order retrieved by ID'
        );
    }

    /**
     * @depends testCreateOrder
     */
    public function testGetAllOrders(array $order): void
    {
        $this->client->request(
            'GET',
            '/orders',
            [],
            [],
            ['HTTP_Authorization' => 'Bearer ' . $order['userId']]
        );
        $this->assertResponseIsSuccessful('Failed to retrieve all orders');
        $orders = json_decode($this->client->getResponse()->getContent(), true);
        $activeOrders = array_filter($orders, fn($o) => $o['active']);
        $retrievedOrder = array_values($activeOrders)[0];
        $this->assertOrderEquals(
            $order,
            $retrievedOrder,
            'The first active order from the orders list does not match the retrieved user\'s active order'
        );
    }

    /**
     * @depends testCreateOrder
     */
    public function testCancelOrder(array $order): void
    {
        $this->assertOrderStatus($order['id'], true, $order['userId']);

        $this->changeOrderStatus($order['id'], 'cancel', $order['userId']);

        $this->assertOrderStatus($order['id'], false, $order['userId']);

        $this->changeOrderStatus($order['id'], 'restore', $order['userId']); // cleanup

        $this->assertOrderStatus($order['id'], true, $order['userId']);  // cleanup
    }

    /**
     * @depends testCreateOrder
     */
    public function testRestoreOrder(array $order): void
    {
        //if the order is already active, cancel it first
        $this->changeOrderStatus($order['id'], 'cancel', $order['userId']);

        $this->assertOrderStatus($order['id'], false, $order['userId']);

        $this->changeOrderStatus($order['id'], 'restore', $order['userId']);

        $this->assertOrderStatus($order['id'], true, $order['userId']);

        $this->changeOrderStatus($order['id'], 'cancel', $order['userId']); // cleanup

        $this->assertOrderStatus($order['id'], false, $order['userId']);  // cleanup
    }

    private function assertOrderStatus(int $orderId, bool $expectedStatus, string $userId): void
    {
        $this->client->request(
            'GET',
            '/orders/' . $orderId,
            [],
            [],
            ['HTTP_Authorization' => 'Bearer ' . $userId]
        );
        $this->assertResponseIsSuccessful('Failed to retrieve order');
        $order = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(
            $expectedStatus,
            $order['active'],
            'The order status does not match the expected status (expected: '
            . ($expectedStatus ? 'active' : 'inactive') . ')'
        );
    }

    private function changeOrderStatus(int $orderId, string $newStatus, string $userId): void
    {
        $this->client->request(
            'PUT',
            '/orders/' . $newStatus . '/' . $orderId,
            [],
            [],
            ['HTTP_Authorization' => 'Bearer ' . $userId]
        );
        $this->assertResponseIsSuccessful('Failed to change order status to ' . $newStatus);
    }

    private function assertOrderEquals(array $expectedOrder, array $actualOrder, string $message = ''): void
    {
        unset($expectedOrder['createdAt'], $actualOrder['createdAt']);
        $this->assertEquals($expectedOrder, $actualOrder, $message);
    }
}
