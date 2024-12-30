<?php

namespace App\Repository;

use App\Entity\CategoriePosts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategoriePosts>
 */
class CategoriePostsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoriePosts::class);
    }

    //    /**
    //     * @return CategoriePosts[] Returns an array of CategoriePosts objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    // /**
    // * @return CategoriePosts[] Returns an array of CategoriePosts objects
    // */
    //     public function findOneCategorieMain()
    //    {
    //        return $this->createQueryBuilder('c')
    //         ->leftJoin('c post', 'p')
    //         ->addSelect('p')
    //         ->setMaxResults(3)
    //         ->orderBy('c.id', 'DESC')
    //         ->getQuery()
              
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CategoriePosts
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
