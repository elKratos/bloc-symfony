<?php


namespace App\Repository;

use App\Entity\Product;
use App\Entity\TypeFlower;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * Class ProductRepository
 * @package App\Repository
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * ProductRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param int $page
     * @param TypeFlower|null $typeFlower
     * @return Paginator
     * @throws Exception
     */
    public function findLatest(int $page = 1, TypeFlower $typeFlower = null): Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('a', 't')
            ->innerJoin('p.author', 'a')
            ->leftJoin('p.type_flower', 't')
            ->where('p.publishedAt <= :now')
            ->orderBy('p.publishedAt', 'DESC')
            ->setParameter('now', new \DateTime())
        ;

        if (null !== $typeFlower) {
            $qb->andWhere(':type MEMBER OF p.type_flower')
                ->setParameter('type_flower', $typeFlower);
        }

        return (new Paginator($qb))->paginate($page);
    }
}