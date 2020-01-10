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
        $from = $date." 00:00:00";
        $to   = $date." 23:59:59";

        return $this->createQueryBuilder('e')
            ->select('count(e) as number','c.name')
            ->leftJoin('e.categories', 'c')
            ->where('e.date BETWEEN :from and :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
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
            ->join('e.categories', 'c')
            ->groupBy('c.name')
            ->getQuery()
            ->getResult();
    }

    public function getAllEventsOfCategory($category)
    {
        return $this->createQueryBuilder('e')
            ->select('e','count(u) as participant','c.name')
            ->leftJoin('e.categories', 'c')
            ->leftJoin('e.id_users', 'u')
            ->where('c.name = :categorie')
            ->setParameter('categorie', $category)
            ->groupBy('e.id')
            ->getQuery()
            ->getResult();
    }

    public function getAllEventsByDate($date)
    {
        $from = $date." 00:00:00";
        $to   = $date." 23:59:59";

        return $this->createQueryBuilder('e')
            ->select('e','count(u) as participant','c.name')
            ->leftJoin('e.categories', 'c')
            ->leftJoin('e.id_users', 'u')
            ->where('e.date BETWEEN :from and :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->groupBy('e.id')
            ->getQuery()
            ->getResult();
    }

    public function getAllEventsOfCity($city)
    {
        return $this->createQueryBuilder('e')
            ->select('e','count(u) as participant','c.name')
            ->leftJoin('e.categories', 'c')
            ->leftJoin('e.id_users', 'u')
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
            ->leftJoin('e.id_users', 'u')
            ->groupBy('e.id')
            ->getQuery()
            ->getResult();
    }

    public function getUserParticipations($user_id)
    {
        return $this->createQueryBuilder('e')
            ->select('e','count(u) as participant','c.name')
            ->leftJoin('e.categories', 'c')
            ->leftJoin('e.id_users', 'u')
            ->where('u.id = :user')
            ->setParameter('user', $user_id)
            ->groupBy('e.id')
            ->getQuery()
            ->getResult();
    }

    public function getSingleEvent($event_id)
    {
        return $this->createQueryBuilder('e')
            ->select('e','count(u) as participant','c.name')
            ->leftJoin('e.categories', 'c')
            ->leftJoin('e.id_users', 'u')
            ->where('e.id = :event')
            ->setParameter('event', $event_id)
            ->getQuery()
            ->getResult();
    }

}
