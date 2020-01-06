<?php

namespace App\Repository;

use App\Entity\Events;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Events|null find($id, $lockMode = null, $lockVersion = null)
 * @method Events|null findOneBy(array $criteria, array $orderBy = null)
 * @method Events[]    findAll()
 * @method Events[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Events::class);
    }

    // /**
    //  * @return Events[] Returns an array of Events objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Events
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getNbEvents()
    {
        return $this->createQueryBuilder('e')
            ->select('count(e)')
            ->getQuery()
            ->getSingleScalarResult();
            ;
    }

    public function getNbEventsAtDate($date)
    {
        return $this->createQueryBuilder('e')
            ->select('count(e) as number','c.name')
            ->leftJoin('e.categories', 'c')
            ->where('e.date = :date')
            ->setParameter('date', $date)
            ->groupBy('c.name')
            ->getQuery()
            ->getResult();
    }

    public function getNbEventsWithThisCity($city)
    {
        return $this->createQueryBuilder('e')
            ->select('count(e) as number','c.name')
            ->leftJoin('e.categories', 'c')
            ->where('e.city = :city')
            ->setParameter('city', $city)
            ->groupBy('c.name')
            ->getQuery()
            ->getResult();
    }

    public function getEventsGroupByCategories()
    {
        return $this->createQueryBuilder('e')
            ->select('count(e) as number','c.name')
            ->leftJoin('e.categories', 'c')
            ->groupBy('c.name')
            ->getQuery()
            ->getResult();
    }

    public function getAllEventsOfCategory($category)
    {
        return $this->createQueryBuilder('e')
            ->select('e','count(u) as participant','c.name')
            ->leftJoin('e.categories', 'c')
            ->leftJoin('e.id_user', 'u')
            ->where('c.name = :categorie')
            ->setParameter('categorie', $category)
            ->groupBy('e.id')
            ->getQuery()
            ->getResult();
    }

    public function getAllEventsByDate($date)
    {
        return $this->createQueryBuilder('e')
            ->select('e','count(u) as participant','c.name')
            ->leftJoin('e.categories', 'c')
            ->leftJoin('e.id_user', 'u')
            ->where('e.date = :date')
            ->setParameter('date', $date)
            ->groupBy('e.id')
            ->getQuery()
            ->getResult();
    }

    public function getAllEventsOfCity($city)
    {
        return $this->createQueryBuilder('e')
            ->select('e','count(u) as participant','c.name')
            ->leftJoin('e.categories', 'c')
            ->leftJoin('e.id_user', 'u')
            ->where('e.city = :city')
            ->setParameter('city', $city)
            ->groupBy('e.id')
            ->getQuery()
            ->getResult();
    }

    public function getAllEvents()
    {
        return $this->createQueryBuilder('e')
            ->select('e','count(u) as participant','c.name')
            ->leftJoin('e.categories', 'c')
            ->leftJoin('e.id_user', 'u')
            ->groupBy('e.id')
            ->getQuery()
            ->getResult();
    }

    public function getUserParticipations()
    {

    }

    public function getSingleEvent($id)
    {
        
    }

}
