<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Product",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer"),
        new OA\Property(property: "title", type: "string"),
        new OA\Property(property: "description", type: "string"),
        new OA\Property(property: "price", type: "number"),
        new OA\Property(property: "category", type: "string"),
        new OA\Property(property: "image", type: "string"),
        new OA\Property(property: "rating", ref: "#/components/schemas/Rating"),
        new OA\Property(property: "available", type: "boolean")
    ]
)]
#[ORM\Entity]
#[ORM\Table(name: '"product"')]
final class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $title;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $description;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank]
    private float $price;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $category;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $image;

    #[ORM\Embedded(class: Rating::class)]
    private Rating $rating;

    #[ORM\ManyToMany(targetEntity: Order::class, mappedBy: 'products')]
    private Collection $orders;

    #[ORM\OneToMany(targetEntity: OrderProduct::class, mappedBy: 'product')]
    private Collection $orderProducts;

    private bool $available = true;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->orderProducts = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function getRating(): Rating
    {
        return $this->rating;
    }

    public function setRating(Rating $rating): void
    {
        $this->rating = $rating;
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }
}
