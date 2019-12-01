<?php

namespace App\Repository;
use App\Entity\Domain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Domain|null find($id, $lockMode = null, $lockVersion = null)
 * @method Domain|null findOneBy(array $criteria, array $orderBy = null)
 * @method Domain[]    findAll()
 * @method Domain[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Domain::class);
    }


    /**
     * @param $currentUser
     * @return Domain[] Returns an array of Domain objects
     */

    public function getDomainsByUser($currentUser)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.users = :val')
            ->setParameter('val', $currentUser)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(15)
            ->getQuery()
            ->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Domain
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
