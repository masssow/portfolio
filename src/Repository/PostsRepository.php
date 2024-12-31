<?php

namespace App\Repository;

use App\Entity\CategoriePosts;
use App\Entity\Posts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Posts>
 */
class PostsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Posts::class);
    }

    //    /**
    //     * @return Posts[] Returns an array of Posts objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    // public function findByCategorie(int $categorieId)
    // {
    //     return $this->createQueryBuilder('p')
    //         ->innerJoin('p.categoriePosts', 'c')
    //         ->where('c.id = :categorieI')
    //         ->setParameter('categorieId', $categorieId)
    //         ->orderBy('p.createdAt', 'DESC')
    //         ->getQuery();
    // }
    public function findByCategorie(?int $categorieId)
    {
        $qb = $this->createQueryBuilder('p')
        ->innerJoin('p.categoriePosts', 'c');

        if ($categorieId) {
            $qb->where('c.id = :categorieId')
                ->setParameter('categorieId', $categorieId);
        }

        return $qb->orderBy('p.createdAt', 'DESC')
            ->getQuery();
    }

       public function findWithPaginator()
       {
           return $this->createQueryBuilder('a')
               ->orderBy('a.createdAt', 'DESC')
               ->getQuery()
           ;
       }

    public function countPostsByCategorie()
    {
        return $this->createQueryBuilder('p')
            ->select('c.id, COUNT(p.id) as postCount')
            ->innerJoin('p.categoriePosts', 'c')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }

    
      
       public function findNextPost(Posts $currentPost): ?Posts
       {
           return $this->createQueryBuilder('p')
               ->andWhere('p.createdAt > :currentDate')
               ->setParameter('currentDate', $currentPost->getCreatedAt() )
               ->orderBy('p.createdAt', 'ASC')
               ->setMaxResults(1)
               ->getQuery()
               ->getOneOrNullResult()
           ;
       }

       public function findPreviusPost(Posts $currentPost): ?Posts
       {
           return $this->createQueryBuilder('p')
                ->andWhere('p.createdAt < :currentDate')
                ->setParameter('currentDate', $currentPost->getCreatedAt())
                ->orderBy('p.createdAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
           ;
       }

    public function findPopularPost(int $limit = 4 ): array
    {
        return $this->createQueryBuilder('p')
            ->select('p, COUNT(m.id) AS HIDDEN commentCount')
            ->leftJoin('p.messages', 'm') 
            ->where('p.createdAt <= :now')
            ->setParameter('now', new \DateTime()) 
            ->groupBy('p.id')
            ->orderBy('commentCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }



     //    /**
    //     * @return Posts[] Returns an array of Posts objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
}
