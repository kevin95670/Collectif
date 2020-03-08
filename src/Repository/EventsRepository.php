<?php

namespace App\Repository;

use App\Entity\Events;
use App\Data\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Events|null find($id, $lockMode = null, $lockVersion = null)
 * @method Events|null findOneBy(array $criteria, array $orderBy = null)
 * @method Events[]    findAll()
 * @method Events[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventsRepository extends ServiceEntityRepository
{

    private $paginator;

    public function __construct(ManagerRegistry $registry,PaginatorInterface $paginator)
    {
        parent::__construct($registry, Events::class);
        $this->paginator = $paginator;
    }

    public function getNbEvents()
    {
        return $this->createQueryBuilder('e')
            ->select('count(e)')
            ->getQuery()
            ->getSingleScalarResult();
            ;
    }

    /*public function getNbEventsAtDate($date)
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
    }*/

    /*public function getNbEventsWithThisCity($city)
    {
        return $this->createQueryBuilder('e')
            ->select('count(e) as number','c.name')
            ->leftJoin('e.categories', 'c')
            ->where('e.city = :city')
            ->setParameter('city', $city)
            ->groupBy('c.name')
            ->getQuery()
            ->getResult();
    }*/

    public function getEventsGroupByCategories()
    {
        return $this->createQueryBuilder('e')
            ->select('count(e) as number','c.name')
            ->join('e.categories', 'c')
            ->groupBy('c.name')
            ->getQuery()
            ->getResult();
    }

    public function getTwoLastEvents()
    {
        return $this->createQueryBuilder('e')
            ->select('e','count(u) as participant','c.name')
            ->leftJoin('e.categories', 'c')
            ->leftJoin('e.id_users', 'u')
            ->orderBy('e.date', 'desc')
            ->groupBy('e.id')
            ->setMaxResults(2)
            ->getQuery()
            ->getResult();
    }

    public function getAllEventsOfCategory($category, SearchData $search)
    {
        $query = $this->createQueryBuilder('e')
            ->select('e','count(u) as participant','c.name')
            ->leftJoin('e.categories', 'c')
            ->leftJoin('e.id_users', 'u')
            ->where('c.name = :categorie')
            ->setParameter('categorie', $category);


            if(!empty($search->query)){
                $query = $query
                    ->where('e.name LIKE :query')
                    ->setParameter('query', "%{$search->query}%");
            }

            if(!empty($search->city)){
                $query = $query
                    ->andWhere('e.city LIKE :city')
                    ->setParameter('city', "%{$search->city}%");
            }

            if(!empty($search->date)){
                $from = $search->date." 00:00:00";
                $to   = $search->date." 23:59:59";
                $query = $query
                    ->andWhere('e.date BETWEEN :from and :to')
                    ->setParameter('from', $from)
                    ->setParameter('to', $to);
            }

            $query = $query
                ->groupBy('e.id');

            $query = $query->getQuery();
            return $this->paginator->paginate(
            $query, /* query NOT result */
            $search->page, /*page number*/
            5
        );

    }

    /*public function getAllEventsByDate($date)
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
            ->getQuery();
            //->getResult();

    }*/

    /*public function getAllEventsOfCity($city)
    {
        return $this->createQueryBuilder('e')
            ->select('e','count(u) as participant','c.name')
            ->leftJoin('e.categories', 'c')
            ->leftJoin('e.id_users', 'u')
            ->where('e.city = :city')
            ->setParameter('city', $city)
            ->groupBy('e.id')
            ->getQuery();
            //->getResult();
    }*/

    public function getAllEvents(SearchData $search)
    {

        $query = $this->createQueryBuilder('e')
            ->select('e','count(u) as participant','c.name')
            ->leftJoin('e.categories', 'c')
            ->leftJoin('e.id_users', 'u');

            if(!empty($search->query)){
                $query = $query
                    ->where('e.name LIKE :query')
                    ->setParameter('query', "%{$search->query}%");
            }

            if(!empty($search->city)){
                $query = $query
                    ->andWhere('e.city LIKE :city')
                    ->setParameter('city', "%{$search->city}%");
            }

            if(!empty($search->date)){
                $from = $search->date." 00:00:00";
                $to   = $search->date." 23:59:59";
                $query = $query
                    ->andWhere('e.date BETWEEN :from and :to')
                    ->setParameter('from', $from)
                    ->setParameter('to', $to);
            }

            if(!empty($search->categories)){
                $query = $query
                    ->andWhere('c.id IN (:categories)')
                    ->setParameter('categories', $search->categories);
            }

            $query = $query
            ->groupBy('e.id');

            $query = $query->getQuery();
            return $this->paginator->paginate(
            $query, /* query NOT result */
            $search->page, /*page number*/
            5 /*limit per page*/
        );
            //->getResult();
    }

    public function getUserParticipations($user_id, SearchData $search)
    {
        $query = $this->createQueryBuilder('e')
            ->select('e','count(u) as participant','c.name')
            ->leftJoin('e.categories', 'c')
            ->leftJoin('e.id_users', 'u')
            ->where('u.id = :user')
            ->setParameter('user', $user_id);

            if(!empty($search->query)){
                $query = $query
                    ->where('e.name LIKE :query')
                    ->setParameter('query', "%{$search->query}%");
            }

            if(!empty($search->city)){
                $query = $query
                    ->andWhere('e.city LIKE :city')
                    ->setParameter('city', "%{$search->city}%");
            }

            if(!empty($search->date)){
                $from = $search->date." 00:00:00";
                $to   = $search->date." 23:59:59";
                $query = $query
                    ->andWhere('e.date BETWEEN :from and :to')
                    ->setParameter('from', $from)
                    ->setParameter('to', $to);
            }

            if(!empty($search->categories)){
                $query = $query
                    ->andWhere('c.id IN (:categories)')
                    ->setParameter('categories', $search->categories);
            }

            $query = $query
                ->groupBy('e.id');
            //->getResult();

            $query = $query->getQuery();
            return $this->paginator->paginate(
            $query, /* query NOT result */
            $search->page, /*page number*/
            5 /*limit per page*/
        );
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
