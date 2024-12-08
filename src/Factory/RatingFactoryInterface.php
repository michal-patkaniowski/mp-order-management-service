<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Rating;

interface RatingFactoryInterface
{
    public function createFromArray(array $data): Rating;
}
