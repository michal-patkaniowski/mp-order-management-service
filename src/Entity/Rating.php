<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Rating",
    type: "object",
    properties: [
        new OA\Property(property: "rate", type: "number"),
        new OA\Property(property: "count", type: "integer")
    ]
)]
#[ORM\Embeddable]
class Rating
{
    #[ORM\Column(type: 'float')]
    private float $rate;

    #[ORM\Column(type: 'integer')]
    private int $count;

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }
}
