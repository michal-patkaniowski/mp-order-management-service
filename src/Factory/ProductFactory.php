<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Product;
use App\Entity\Rating;
use App\Factory\RatingFactoryInterface;

class ProductFactory implements ProductFactoryInterface
{
    public function __construct(private RatingFactoryInterface $ratingFactory)
    {
    }


    public function createFromArray(array $data): Product
    {
        $product = new Product();
        $product->setId($data['id']);
        $product->setTitle($data['title']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);
        $product->setCategory($data['category']);
        $product->setImage($data['image']);
        $product->setRating($this->ratingFactory->createFromArray($data['rating']));
        $product->setAvailable(true);

        return $product;
    }
}
