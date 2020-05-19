<?php

namespace App\Repository;

use App\Entity\TrickPicture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrickPicture|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrickPicture|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrickPicture[]    findAll()
 * @method TrickPicture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickPictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrickPicture::class);
    }

    // /**
    //  * @return TrickPicture[] Returns an array of TrickPicture objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrickPicture
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
