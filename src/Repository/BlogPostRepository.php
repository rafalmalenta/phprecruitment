<?php

namespace App\Repository;

use App\Entity\BlogPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\String\b;

/**
 * @method BlogPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogPost[]    findAll()
 * @method BlogPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

     /**
      * @return BlogPost[] Returns an array of BlogPost objects
      */
    public function findAllPaginated($page, $limit): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.id', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult(($page-1) * $limit)
            ->getQuery()
            ->getResult();
    }
    /**
     * @return int Returns an array of BlogPost objects
     */
    public function postsCount(): int
    {
        try {
            return $this->createQueryBuilder('b')
                ->select('COUNT(b) as sum')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e)
        {

        }
    }

    /*
    public function findOneBySomeField($value): ?BlogPost
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
