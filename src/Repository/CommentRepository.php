<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }
    /**
    * @return Comment[] Returns an array of BlogPost objects
    */
    public function findAllPaginated($page, $limit): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult(($page-1) * $limit)
            ->getQuery()
            ->getResult();
    }
    /**
     * @return Comment[] Returns an array of BlogPost objects
     */
    public function findAllPaginatedWithOwnerId($id,$page, $limit): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->andWhere('c.post = :param')
            ->setParameter('param', $id)
            ->setMaxResults($limit)
            ->setFirstResult(($page-1) * $limit)
            ->getQuery()
            ->getResult();
    }
    /**
     * @return int Returns an array of BlogPost objects
     */
    public function commentsCount(): int
    {
        try {
            return $this->createQueryBuilder('c')
                ->select('COUNT(c) as count')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e)
        {

        }
    }
    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
