<?php

declare(strict_types=1);

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrdersControllerTest extends WebTestCase
{
    public function testCreateOrder()
    {
        $client = static::createClient();
        $client->request('POST', '/orders');
        $this->assertResponseIsSuccessful('Failed to create order');
        $order1 = json_decode($client->getResponse()->getContent(), true);
        return $order1;
    }

    /**
     * @depends testCreateOrder
     */
    public function testGetOrderById($order1)
    {
        $client = static::createClient();
        $client->request('GET', '/orders/' . $order1['id']);
        $this->assertResponseIsSuccessful('Failed to retrieve order by ID');
        $order2 = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($order1, $order2, 'The order retrieved by ID does not match the created order');
        return $order2;
    }

    /**
     * @depends testGetOrderById
     */
    public function testGetActiveOrder($order2)
    {
        $client = static::createClient();
        $client->request('GET', '/orders/active');
        $this->assertResponseIsSuccessful('Failed to retrieve active order');
        $order3 = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(
            $order2,
            $order3,
            'The retrieved user\'s active order does not match the order retrieved by ID'
        );
        return $order3;
    }

    /**
     * @depends testGetActiveOrder
     */
    public function testGetAllOrders($order3)
    {
        $client = static::createClient();
        $client->request('GET', '/orders');
        $this->assertResponseIsSuccessful('Failed to retrieve all orders');
        $orders = json_decode($client->getResponse()->getContent(), true);
        $activeOrders = array_filter($orders, fn($order) => $order['active']);
        $order4 = array_values($activeOrders)[0];
        $this->assertEquals(
            $order3,
            $order4,
            'The first active order from the orders list does not match the retrieved user\'s active order'
        );
        return $order4;
    }

    /**
     * @depends testGetActiveOrder
     */
    public function testCancelOrder($order3)
    {
        $client = static::createClient();
        $newStatus = 'cancel';
        $client->request('PUT', '/orders/' . $newStatus . '/' . $order3['id']);
        $this->assertResponseIsSuccessful('Failed to cancel order');

        $client->request('GET', '/orders/' . $order3['id']);
        $this->assertResponseIsSuccessful('Failed to retrieve order by ID after status change');
        $updatedOrder = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(false, $updatedOrder['active'], 'Order has not been canceled correctly');
    }

    /**
     * @depends testGetAllOrders
     */
    public function testRestoreOrder($order4)
    {
        $client = static::createClient();

        $client->request('GET', '/orders');
        $orders = json_decode($client->getResponse()->getContent(), true);
        $inactiveOrder = array_filter($orders, fn($order) => !$order['active'])[0];

        $newStatus = 'restore';
        $client->request('PUT', '/orders/' . $newStatus . '/' . $inactiveOrder['id']);
        $this->assertResponseIsSuccessful('Failed to restore order');

        $client->request('GET', '/orders/' . $inactiveOrder['id']);
        $this->assertResponseIsSuccessful('Failed to retrieve order by ID after restoring');
        $restoredOrder = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(true, $restoredOrder['active'], 'Order has not been restored correctly');
    }
}
