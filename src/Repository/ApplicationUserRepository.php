<?php

namespace App\Repository;

use App\Entity\ApplicationUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ApplicationUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApplicationUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApplicationUser[]    findAll()
 * @method ApplicationUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ApplicationUser::class);
    }

    // /**
    //  * @return ApplicationUser[] Returns an array of ApplicationUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ApplicationUser
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
