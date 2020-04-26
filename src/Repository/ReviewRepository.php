<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    // /**
    //  * @return Review[] Returns an array of Review objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Review
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getReviewsOfEvent($event_id)
    {
        return $this->createQueryBuilder('r')
            ->select('r')
            ->join('r.user', 'u')
            ->join('r.event', 'e')
            ->where('e.id = :event')
            ->setParameter('event', $event_id)
            ->orderBy('r.created_at', 'asc')
            ->getQuery()
            ->getResult();
    }

    public function getAverageOfEvent($event_id)
    {
        return $this->createQueryBuilder('r')
            ->select('avg(r.note) as moyenne')
            ->join('r.user', 'u')
            ->join('r.event', 'e')
            ->where('e.id = :event')
            ->setParameter('event', $event_id)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
