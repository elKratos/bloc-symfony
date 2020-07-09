<?php


namespace App\Repository;


use App\Entity\TypeFlower;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class TypeFlowerRepository
 * @package App\Repository
 */
class TypeFlowerRepository extends ServiceEntityRepository
{
    /**
     * TypeFlowerRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeFlower::class);
    }
}