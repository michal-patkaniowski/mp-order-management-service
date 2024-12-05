<?php

declare(strict_types=1);

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrdersControllerTest extends WebTestCase
{
    private $client;

    private const REFERENCE_ORDER = [
        'id' => 86,
        'userId' => '30b3c1b6cb426932fd5ff00345880aef',
        'active' => true,
        'orderProducts' => [],
    ];

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetOrderById(): void
    {
        $this->client->request(
            'GET',
            '/orders/' . self::REFERENCE_ORDER['id'],
            [],
            [],
            ['HTTP_Authorization' => 'Bearer ' . self::REFERENCE_ORDER['userId']]
        );
        $this->assertResponseIsSuccessful('Failed to retrieve order by ID');
        $order = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(
            self::REFERENCE_ORDER['id'],
            $order['id'],
            'The order retrieved by ID does not match the created order'
        );
        $this->assertEquals(
            self::REFERENCE_ORDER['active'],
            $order['active'],
            'The order retrieved by ID is not active'
        );
        $this->assertEquals(
            self::REFERENCE_ORDER['userId'],
            $order['userId'],
            'The order retrieved by ID does not belong to the user'
        );
    }

    public function testGetActiveOrder(): void
    {
        $this->client->request(
            'GET',
            '/orders/active',
            [],
            [],
            ['HTTP_Authorization' => 'Bearer ' . self::REFERENCE_ORDER['userId']]
        );
        $this->assertResponseIsSuccessful('Failed to retrieve active order');
        $order = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertOrderEquals(
            self::REFERENCE_ORDER,
            $order,
            'The retrieved user\'s active order does not match the order retrieved by ID'
        );
    }

    public function testGetAllOrders(): void
    {
        $this->client->request(
            'GET',
            '/orders',
            [],
            [],
            ['HTTP_Authorization' => 'Bearer ' . self::REFERENCE_ORDER['userId']]
        );
        $this->assertResponseIsSuccessful('Failed to retrieve all orders');
        $orders = json_decode($this->client->getResponse()->getContent(), true);
        $activeOrders = array_filter($orders, fn($order) => $order['active']);
        $order = array_values($activeOrders)[0];
        $this->assertOrderEquals(
            self::REFERENCE_ORDER,
            $order,
            'The first active order from the orders list does not match the retrieved user\'s active order'
        );
    }

    public function testCancelOrder(): void
    {
        $this->assertOrderStatus(self::REFERENCE_ORDER['id'], true);

        $this->changeOrderStatus(self::REFERENCE_ORDER['id'], 'cancel');

        $this->assertOrderStatus(self::REFERENCE_ORDER['id'], false);

        $this->changeOrderStatus(self::REFERENCE_ORDER['id'], 'restore'); // cleanup

        $this->assertOrderStatus(self::REFERENCE_ORDER['id'], true);  // cleanup
    }

    public function testRestoreOrder(): void
    {
        //if the order is already active, cancel it first
        if (self::REFERENCE_ORDER['active']) {
            $this->changeOrderStatus(self::REFERENCE_ORDER['id'], 'cancel');
        }

        $this->assertOrderStatus(self::REFERENCE_ORDER['id'], false);

        $this->changeOrderStatus(self::REFERENCE_ORDER['id'], 'restore');

        $this->assertOrderStatus(self::REFERENCE_ORDER['id'], true);

        $this->changeOrderStatus(self::REFERENCE_ORDER['id'], 'cancel'); // cleanup

        $this->assertOrderStatus(self::REFERENCE_ORDER['id'], false);  // cleanup
    }

    public function testCreateOrder(): void
    {
        $this->client->request('POST', '/orders');
        $this->assertResponseIsSuccessful('Failed to create order');
        $order = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertNotEquals(
            self::REFERENCE_ORDER['id'],
            $order['id'],
            'The new order id matches the reference order id'
        );
        $this->assertEquals(true, $order['active'], 'The new order is not active');

        $this->changeOrderStatus(self::REFERENCE_ORDER['id'], 'restore'); // cleanup
    }

    private function assertOrderStatus(int $orderId, bool $expectedStatus): void
    {
        $this->client->request(
            'GET',
            '/orders/' . $orderId,
            [],
            [],
            ['HTTP_Authorization' => 'Bearer ' . self::REFERENCE_ORDER['userId']]
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

    private function changeOrderStatus(int $orderId, string $newStatus): void
    {
        $this->client->request(
            'PUT',
            '/orders/' . $newStatus . '/' . $orderId,
            [],
            [],
            ['HTTP_Authorization' => 'Bearer ' . self::REFERENCE_ORDER['userId']]
        );
        $this->assertResponseIsSuccessful('Failed to change order status to ' . $newStatus);
    }

    private function assertOrderEquals(array $expectedOrder, array $actualOrder, string $message = ''): void
    {
        unset($expectedOrder['createdAt'], $actualOrder['createdAt']);
        $this->assertEquals($expectedOrder, $actualOrder, $message);
    }
}
