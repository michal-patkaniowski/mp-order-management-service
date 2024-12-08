<?php

declare(strict_types=1);

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductsControllerTest extends WebTestCase
{
    private const KEYS_TO_CHECK = ['id', 'title', 'price', 'category', 'image', 'available'];

    public static function productDataProvider(): array
    {
        $products = [
            [
                [
                    "id" => 1,
                    "title" => "Fjallraven - Foldsack No. 1 Backpack, Fits 15 Laptops",
                    "price" => 109.95,
                    "category" => "men's clothing",
                    "description" => "Your perfect pack for everyday use and walks in the forest. Stash your laptop (up to 15 inches) in the padded sleeve, your everyday essentials in the main compartment.",
                    "image" => "https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg",
                    "available" => true,
                ]
            ],
            [
                [
                    "id" => 2,
                    "title" => "Mens Casual Premium Slim Fit T-Shirts ",
                    "price" => 22.30,
                    "category" => "men's clothing",
                    "description" => "Slim-fitting style, contrast raglan long sleeve, three-button henley placket, lightweight & soft fabric for breathable and comfortable wear. Solid stitched shirts with round neck for durability.",
                    "image" => "https://fakestoreapi.com/img/71-3HjGNDUL._AC_SY879._SX._UX._SY._UY_.jpg",
                    "available" => true,
                ]
            ],
            [
                [
                    "id" => 3,
                    "title" => "Mens Cotton Jacket",
                    "price" => 55.99,
                    "category" => "men's clothing",
                    "description" => "Great outerwear for Spring/Autumn/Winter, suitable for activities like hiking, camping, or cycling. A warm-hearted gift for loved ones.",
                    "image" => "https://fakestoreapi.com/img/71li-ujtlUL._AC_UX679_.jpg",
                    "available" => true,
                ]
            ],
            [
                [
                    "id" => 4,
                    "title" => "Mens Casual Slim Fit",
                    "price" => 15.99,
                    "category" => "men's clothing",
                    "description" => "The color may vary between screen and real life. Detailed size information is available in the product description.",
                    "image" => "https://fakestoreapi.com/img/71YXzeOuslL._AC_UY879_.jpg",
                    "available" => true,
                ]
            ]
        ];
        return $products;
    }

    /**
     * @dataProvider productDataProvider
     */
    public function testGetProduct($product): void
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/products/' . $product['id']
        );
        $this->assertResponseIsSuccessful('Failed to retrieve product by ID');
        $retrievedProduct = json_decode($client->getResponse()->getContent(), true);
        $this->assertProductData($product, $retrievedProduct, self::KEYS_TO_CHECK);
    }

    public function testGetAllProducts(): void
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/products'
        );
        $this->assertResponseIsSuccessful('Failed to retrieve all products');
        $products = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($products, 'No products retrieved');
    }

    private function assertProductData(array $expected, array $actual, array $keys): void
    {
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $actual, "Key '$key' is missing in the actual product data");
            $this->assertEquals($expected[$key], $actual[$key], "Value for key '$key' does not match");
        }
    }

}
