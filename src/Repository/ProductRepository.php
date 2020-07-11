<?php


namespace App\Repository;

use App\Entity\Product;
use App\Entity\TypeFlower;
use App\Pagination\Paginator;
use DateTime;
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
     * @param string $text
     * @return array
     */
    public function getByText(string $text):array
    {
        $query = $this->createQueryBuilder('p')
            ->where("p.title LIKE :title")
            ->orderBy("p.publishedAt", "DESC")
            ->setParameter('title', "%$text%")
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param string $start
     * @param string $end
     * @return array
     * @throws Exception
     */
    public function getByDate(string $start, string $end):array
    {
        $start = new DateTime($start);
        $end = new DateTime($end);

        $query = $this->createQueryBuilder('p')
            ->where("p.publishedAt BETWEEN :start AND :end")
            ->orderBy("p.publishedAt", "DESC")
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param int $id
     * @return array
     */
    public function getByType(int $id):array
    {
        $query = $this->createQueryBuilder('p')
            ->where("p.typeFlower = :id")
            ->orderBy("p.publishedAt", "DESC")
            ->setParameter(':id', $id)
            ->getQuery();

        return $query->getResult();
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
            ->setParameter('now', new DateTime())
        ;

        if (null !== $typeFlower) {
            $qb->andWhere(':type MEMBER OF p.type_flower')
                ->setParameter('type_flower', $typeFlower);
        }

        return (new Paginator($qb))->paginate($page);
    }
}