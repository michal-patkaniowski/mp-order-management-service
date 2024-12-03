<?php

declare(strict_types=1);

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrdersControllerTest extends WebTestCase
{
    /**
     * @dataProvider createOrderProvider
     */
    public function testCreateOrder($postData, $expectedStatusCode)
    {
        $client = static::createClient();
        $client->request('POST', '/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($postData));

        $this->assertEquals($expectedStatusCode, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function createOrderProvider(): array
    {
        return [
            [['product_id' => 1, 'quantity' => 2], 201],
            [['product_id' => 2, 'quantity' => 1], 201],
            [['product_id' => 3, 'quantity' => 0], 400], // Example of invalid quantity
            [['product_id' => null, 'quantity' => 1], 400], // Example of missing product_id
        ];
    }
}