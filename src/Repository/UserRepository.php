<?php


namespace App\Repository;


use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class UserRepository
 * @package App\Repository
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * TypeFlowerRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $text
     * @return array
     */
    public function getByText(string $text):array
    {
        $query = $this->createQueryBuilder('u')
            ->where("u.name LIKE :name")
            ->setParameter('name', "%$text%")
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param string $role
     * @return array
     */
    public function getByRole(string $role):array
    {
        $query = $this->createQueryBuilder('u')
            ->where("u.role = :role")
            ->setParameter(':role', $role)
            ->getQuery();

        return $query->getResult();
    }
}