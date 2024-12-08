<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Product;
use App\Entity\Rating;

class RatingFactory implements RatingFactoryInterface
{
    public function createFromArray(array $data): Rating
    {
        $rating = new Rating();
        $rating->setRate($data['rate']);
        $rating->setCount($data['count']);

        return $rating;
    }
}
