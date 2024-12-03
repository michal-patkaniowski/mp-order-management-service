<?php

declare(strict_types=1);

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrdersControllerTest extends WebTestCase
{
    public function testOrderCreation()
    {
        $client = static::createClient();

        // Request 1: Create a new active user order
        $postData = ['product_id' => 1, 'quantity' => 2];
        $client->request('POST', '/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Failed to create order');
        $order1 = json_decode($client->getResponse()->getContent(), true);

        // Request 2: Collect the order with id from the response from request 1
        $client->request('GET', '/orders/' . $order1['id']);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Failed to get order by id');
        $order2 = json_decode($client->getResponse()->getContent(), true);

        // Request 3: Collect the user's active order
        $client->request('GET', '/orders/active');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Failed to get active order');
        $order3 = json_decode($client->getResponse()->getContent(), true);

        // Request 4: Collect all user's orders
        $client->request('GET', '/orders');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Failed to get all orders');
        $orders = json_decode($client->getResponse()->getContent(), true);

        // Assert whether orders from requests 1 and 2 are the same
        $this->assertEquals($order1, $order2, 'Order from request 1 and request 2 do not match');

        // Assert whether orders from requests 2 and 3 are the same
        $this->assertEquals($order2, $order3, 'Order from request 2 and request 3 do not match');

        // Assert whether the first active user's order from request 4 and order from request 3 are the same
        $this->assertEquals(
            $orders[0],
            $order3,
            'First active order from request 4 and order from request 3 do not match'
        );
    }
}
