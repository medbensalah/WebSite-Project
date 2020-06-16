<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return false|string
     * @throws Exception
     */

    public function findUsersPerWeek()
    {
        $day = date('w');
        $week_start = date('Y-m-d', strtotime('now -'.$day.' days'));
        $week_end = date('Y-m-d', strtotime('now +'.(6-$day).' days'));
        $startDate = new \DateTime($week_start);
        $endDate = new \DateTime($week_end);
        $dates = array();
        while($startDate <= $endDate) {
            $dates[$startDate->format('Y-m-d')] = '0';
            $startDate->modify('+1 day');
        }
        $qb = $this->getEntityManager()->createQuery('
            Select u.active, Count(u) as num
            FROM App\Entity\User u INDEX BY u.active
            where u.active <= :weekEnd and u.active >= :weekStart
            group by u.active
            ')
            ->setParameters([
                'weekEnd' => $week_end,
                'weekStart' => $week_start
            ])
        ;
        $result = $qb->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        $resultNeeded = array_map(function($value) { return $value['num']; }, $result);
        foreach ($dates as $date => $active) {
            foreach ($resultNeeded as $item => $value){
                if ($item == $date) {
                    $dates[$date] = $value;
                }
            }
        }
        $activity = array();
        $i=0;
        foreach ($dates as $date => $active) {
            $activity[$i] = $active;
            $i++;
        }
       return json_encode($activity);
    }
    /**
     * @return false|string
     * @throws Exception
     */

    public function findUsersPerMonth()
    {
        $day = date('d') - 1;
        $week_start = date('Y-m-d', strtotime('now -'.$day.' days'));
        $week_end = date('Y-m-d', strtotime('now +'.(30-$day).' days'));
        $startDate = new \DateTime($week_start);
        $endDate = new \DateTime($week_end);
        $dates = array();
        while($startDate <= $endDate) {
            $dates[$startDate->format('Y-m-d')] = '0';
            $startDate->modify('+1 day');
        }
        $qb = $this->getEntityManager()->createQuery('
            Select u.active, Count(u) as num
            FROM App\Entity\User u INDEX BY u.active
            where u.active <= :weekEnd and u.active >= :weekStart
            group by u.active
            ')
            ->setParameters([
                'weekEnd' => $week_end,
                'weekStart' => $week_start
            ])
        ;
        $result = $qb->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        $resultNeeded = array_map(function($value) { return $value['num']; }, $result);
        foreach ($dates as $date => $active) {
            foreach ($resultNeeded as $item => $value){
                if ($item == $date) {
                    $dates[$date] = $value;
                }
            }
        }
        $activity = array();
        $i=0;
        foreach ($dates as $date => $active) {
            $activity[$i] = $active;
            $i++;
        }
       return json_encode($activity);
    }
   /**
     * @return false|string
     * @throws Exception
     */

    public function findUsersPerYear()
    {
        $day = date('z');
        $week_start = date('Y-m-d', strtotime('now -'.$day.' days'));
        $week_end = date('Y-m-d', strtotime('now +'.(365-$day).' days'));
        $startDate = new \DateTime($week_start);
        $endDate = new \DateTime($week_end);
        $dates = array();
        while($startDate <= $endDate) {
            $dates[$startDate->format('Y-m-d')] = '0';
            $startDate->modify('+1 day');
        }
        $qb = $this->getEntityManager()->createQuery('
            Select u.active, Count(u) as num
            FROM App\Entity\User u INDEX BY u.active
            where u.active <= :weekEnd and u.active >= :weekStart
            group by u.active
            ')
            ->setParameters([
                'weekEnd' => $week_end,
                'weekStart' => $week_start
            ])
        ;
        $result = $qb->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        $resultNeeded = array_map(function($value) { return $value['num']; }, $result);
        foreach ($dates as $date => $active) {
            foreach ($resultNeeded as $item => $value){
                if ($item == $date) {
                    $dates[$date] = $value;
                }
            }
        }
        $activity = array();
        $i=0;
        foreach ($dates as $date => $active) {
            $activity[$i] = $active;
            $i++;
        }
       return json_encode($activity);
    }


    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
