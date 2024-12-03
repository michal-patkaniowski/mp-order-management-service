<?php

declare(strict_types=1);

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrdersControllerTest extends WebTestCase
{
    public function testCreateOrder()
    {
        $client = static::createClient();
        $postData = ['product_id' => 1, 'quantity' => 2];
        $client->request('POST', '/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Failed to create order');
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
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Failed to retrieve order by ID');
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
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Failed to retrieve active order');
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
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Failed to retrieve all orders');
        $orders = json_decode($client->getResponse()->getContent(), true);
        $activeOrders = array_filter($orders, fn($order) => $order['active']);
        $order4 = array_values($activeOrders)[0];
        $this->assertEquals(
            $order3,
            $order4,
            'The first active order from the orders list does not match the retrieved user\'s active order'
        );
    }
}
