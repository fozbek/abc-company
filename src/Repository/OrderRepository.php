<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findByUserId($id): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :id')
            ->setParameter('id', $id)
            ->orderBy('o.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function update($id, $data)
    {
        $order = $this->find($id);

        $order->setQuantity($data['quantity']);
        $order->setAddress($data['address']);

        $this->_em->persist($order);
        $this->_em->flush();
    }

    public function insert($data): Order
    {
        $order = new Order();

        $order->setOrderCode($data['order_code']);
        $order->setProduct($data['product']);
        $order->setQuantity($data['quantity']);
        $order->setAddress($data['address']);
        $order->setUser($data['user']);

        $this->_em->persist($order);
        $this->_em->flush();

        return $order;
    }

    // /**
    //  * @return Order[] Returns an array of Order objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
