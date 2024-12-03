<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\OrderProduct;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: "Order",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer"),
        new OA\Property(property: "userId", type: "string"),
        new OA\Property(property: "productIds", type: "array", items: new OA\Items(type: "integer")),
        new OA\Property(property: "status", type: "string"),
        new OA\Property(property: "createdAt", type: "string", format: "date-time"),
        new OA\Property(property: "updatedAt", type: "string", format: "date-time")
    ]
)
]
#[ORM\Entity]
#[ORM\Table(name: '"order"')]
final class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $userId;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $createdAt;

    #[ORM\OneToMany(targetEntity: OrderProduct::class, mappedBy: 'order', cascade: ['persist', 'remove'])]
    private Collection $orderProducts;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $active = false;

    public function __construct()
    {
        $this->orderProducts = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }

    public function addProductToOrder(Product $product, int $quantity): void
    {
        $orderProduct = $this->orderProducts->filter(
            fn(OrderProduct $orderProduct) => $orderProduct->getProduct()->getId() === $product->getId()
        )->first();

        if (!$this->orderProducts->contains($orderProduct)) {
            $orderProduct = new OrderProduct();
            $orderProduct->setProduct($product);
            $orderProduct->setOrder($this);
            $orderProduct->setQuantity($quantity);
            $this->orderProducts->add($orderProduct);
        } else {
            $orderProduct->setQuantity($orderProduct->getQuantity() + $quantity);
        }
    }

    public function removeProductFromOrder(Product $product): void
    {
        $orderProduct = $this->orderProducts->filter(
            fn(OrderProduct $orderProduct) => $orderProduct->getProduct()->getId() === $product->getId()
        )->first();

        if ($this->orderProducts->contains($orderProduct)) {
            $this->orderProducts->removeElement($orderProduct);
        }
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
