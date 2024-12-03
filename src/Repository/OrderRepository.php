<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

final class OrderRepository extends ServiceEntityRepository implements OrderRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @return Order[]
     */
    public function getUserOrders(string $userId): array
    {
        return $this->findBy(['userId' => $userId]);
    }

    public function getOrderById(int $id): Order
    {
        return $this->find($id);
    }

    public function saveOrder(Order $order): Order
    {
        $order->setCreatedAt(new DateTime());
        $this->getEntityManager()->persist($order);
        $this->getEntityManager()->flush();
        return $order;
    }

    /**
     * @return Order[]
     */
    public function findActiveUserOrders(string $userId): array
    {
        return $this->findBy(['userId' => $userId, 'active' => true]);
    }
}
