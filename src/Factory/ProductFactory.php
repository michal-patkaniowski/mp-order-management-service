<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Product;

class ProductFactory implements ProductFactoryInterface
{
    public function createFromArray(array $data): Product
    {
        $product = new Product();
        $product->setId($data['id']);
        $product->setTitle($data['title']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);
        $product->setCategory($data['category']);
        $product->setImage($data['image']);
        $product->setRating($data['rating']);
        $product->setAvailable($data['available']);

        return $product;
    }
}
