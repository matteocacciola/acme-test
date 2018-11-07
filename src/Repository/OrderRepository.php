<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, Order::class);
    }
    
    /**
     * 
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     * @return Order[]
     */
    public function findFinalizedBetweenDates(\DateTimeInterface $start, \DateTimeInterface $end) {
        $qb = $this->createQueryBuilder('orderObj');
        
        return $qb
                ->where($qb->expr()->between('orderObj.statusUpdateDate', ':startDate', ':endDate'))
                ->andWhere($qb->expr()->in('orderObj.status', ':finalized'))
                ->setParameter('startDate', $start)
                ->setParameter('endDate', $end)
                ->setParameter('finalized', array(Order::STATUS_COMPLETED, Order::STATUS_PAID))
                ->getQuery()
                ->getResult()
        ;
    }

}
