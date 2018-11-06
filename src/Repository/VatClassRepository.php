<?php

namespace App\Repository;

use App\Entity\VatClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method VatClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method VatClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method VatClass[]    findAll()
 * @method VatClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VatClassRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, VatClass::class);
    }

}
