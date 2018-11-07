<?php

namespace App\Repository;

use App\Entity\Promotion;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

/**
 * @method Promotion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promotion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promotion[]    findAll()
 * @method Promotion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromotionRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, Promotion::class);
    }

    /**
     * 
     * @param string $type
     * @return type
     */
    public function findActivePromotionsByType($type) {
        $qb = $this->createQueryBuilder('p');

        $dateNow = new \DateTime();
        $dateNow->setTime(0, 0, 0);

        $qb
                ->where(
                        $qb->expr()->andX(
                                $qb->expr()->eq('p.status', true), $qb->expr()->orX(
                                        $qb->expr()->andX(
                                                $qb->expr()->isNull('p.startDate'), $qb->expr()->isNull('p.endDate')
                                        ), $qb->expr()->andX(
                                                $qb->expr()->isNull('p.startDate'), $qb->expr()->gte('p.endDate', ':dateNow')
                                        ), $qb->expr()->andX(
                                                $qb->expr()->lte('p.startDate', ':dateNow'), $qb->expr()->isNull('p.endDate')
                                        ), $qb->expr()->andX(
                                                $qb->expr()->lte('p.startDate', ':dateNow'), $qb->expr()->gte('p.endDate', ':dateNow')
                                        )
                                ), $qb->expr()->eq('p.promotionType', $qb->expr()->literal($type))
                        )
                )
        ;

        $qb->setParameter('dateNow', $dateNow);

        return $qb->getQuery()->getResult();
    }

    /**
     * 
     * @return int
     */
    public function countPromotions() {
        return $this
                        ->createQueryBuilder('p')
                        ->select('COUNT(p)')
                        ->add('orderBy', 'p.id DESC')
                        ->getQuery()
                        ->getSingleScalarResult()
        ;
    }

}
