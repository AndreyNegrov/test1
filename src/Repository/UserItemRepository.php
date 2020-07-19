<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\User;
use App\Entity\UserItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserItem[]    findAll()
 * @method UserItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserItem::class);
    }

    public function randomOtherItems(Item $item, User $user)
    {

        return $this->createQueryBuilder('userItem')
            ->andWhere('userItem.item <> :item')
            ->andWhere('userItem.user = :user')
            ->setParameter('item', $item)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

    }

    // /**
    //  * @return UserItem[] Returns an array of UserItem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserItem
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
