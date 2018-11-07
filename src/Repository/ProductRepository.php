<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Promotion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, Product::class);
    }
    
    /**
     * 
     * @param Promotion $promotion
     * @return Product[]
     */
    public function findProductsOnWhichPromotionIsApplicable(Promotion $promotion, $exclude = array()) {
        $qb = $this->createQueryBuilder('product');

        $qb->select('DISTINCT product')
                ->join(Promotion::class, 'promotion', Expr\Join::WITH, $qb->expr()->eq('promotion.id', $promotion->getId()))
                ->leftJoin('promotion.concernedProducts', 'promotionConcernedProduct')
                ->where($qb->expr()->andX(
                                $qb->expr()->orX(
                                        $qb->expr()->eq('promotion.appliedOnAllProducts', true),
                                        $qb->expr()->andX(
                                                $qb->expr()->eq('promotionConcernedProduct', 'product'),
                                                $qb->expr()->isNotNull('promotionConcernedProduct')
                                        )
                                )
                        )
                )
        ;
        
        if (!(empty($exclude))) {
            $qb->andWhere($qb->expr()->notIn('product.id', ':exclude'))
                    ->setParameter('exclude', $exclude)
            ;
        }

        return $qb->getQuery()->getResult();
    }

}
