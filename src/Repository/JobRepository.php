<?php

namespace App\Repository;

use App\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

     /**
      * @return Job[] Returns an array of Job objects
      */

    public function findAllJobs($offset, $limit = 15)
    {
         $qb = $this->createQueryBuilder('j')
            ->where('j.confirmed = :conf')
            ->setParameter('conf', true)
            ->orderBy('j.created', 'ASC')
            ->setFirstResult($offset);
        if($limit) {
            $qb->setMaxResults($limit);
        }
        return $qb->getQuery()
            ->getResult();

    }

    public function findCustomJob($govs, $cats, $desc, $offset, $limit = 15) {
        $qb = $this->createQueryBuilder('j')
            ->addSelect('j')
            ->leftJoin('j.user','u','WITH','j.user = u.id' )
            ->leftJoin('j.categorie','c','WITH','j.categorie = c.id' );
        if($govs != null)
            $qb ->add('where',$qb->expr()->in('u.gouvernorat', $govs));
        if($cats != null)
            $qb ->add('where',$qb->expr()->in('c.Profession', $cats));
        if($desc!="" and $desc!=null)
            $qb->andWhere('u.nom Like :descr OR u.prenom Like :descr OR j.description Like :descr')
                ->setParameter('descr', '%'.$desc.'%');
          $qb->andWhere('j.confirmed = :conf')
            ->setParameter('conf', true)
            ->orderBy('j.created', 'ASC')
            ->setFirstResult($offset);
        if($limit) {
            $qb->setMaxResults($limit);
        }
        return $qb->getQuery()
            ->getResult();
    }


    public function findCatJob($cat, $offset, $limit = 15) {
         $qb = $this->createQueryBuilder('j')
            ->where('j.categorie = :cat')
            ->setParameter('cat',$cat)
            ->andWhere('j.confirmed = :conf')
            ->setParameter('conf', true)
            ->orderBy('j.created', 'ASC')
            ->setFirstResult($offset);
        if($limit) {
            $qb->setMaxResults($limit);
        }
        return $qb->getQuery()
            ->getResult();
    }


    public function findUserJob($user, $offset, $limit = 15) {
         $qb = $this->createQueryBuilder('j')
            ->where('j.user = :user')
            ->setParameter('user',$user)
            ->andWhere('j.confirmed = :conf')
            ->setParameter('conf', true)
            ->orderBy('j.created', 'ASC')
            ->setFirstResult($offset);
        if($limit) {
            $qb->setMaxResults($limit);
        }
        return $qb->getQuery()
            ->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Job
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
