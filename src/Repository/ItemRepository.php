<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\User;
use App\Entity\UserItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /**
     * @param User $user
     * @return Item|null
     * @throws NonUniqueResultException
     */
    public function findOneNoShowedByUser(User $user): ?Item
    {
        $notItems = $this->createQueryBuilder('item')
            ->leftJoin('item.userItem', 'userItem')
            ->andWhere('userItem.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $request = $this->createQueryBuilder('item')
            ->andWhere('item.status = 0');

        if (count($notItems) > 0) {
            $request->andWhere('item not in (:notItems)')
                ->setParameter('notItems', $notItems);
        }

        return $request
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param User $user
     * @param int $count
     * @return mixed
     */
    public function findByUserRandomNotStudiedWords(User $user, int $count)
    {
        return $this->createQueryBuilder('items')
            ->innerJoin('items.userItem', 'userItem')
            ->andWhere('userItem.user = :user')
            ->andWhere('userItem.status = ' . UserItem::NOT_STUDIED)
            ->orderBy('RANDOM()')
            ->setParameter('user', $user)
            ->setMaxResults($count)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param User $user
     * @param int $count
     * @return mixed
     */
    public function findByUserRandomNotRepeatedWords(User $user, int $count)
    {
        return $this->createQueryBuilder('items')
            ->innerJoin('items.userItem', 'userItem')
            ->andWhere('userItem.user = :user')
            ->andWhere('userItem.status = ' . UserItem::NOT_REPEATED)
            ->orderBy('RANDOM()')
            ->setParameter('user', $user)
            ->setMaxResults($count)
            ->getQuery()
            ->getArrayResult();
    }

    public function findAllByUser(User $user) {
        return $this->createQueryBuilder('item')
            ->leftJoin('item.userItem', 'userItem')
            ->andWhere('userItem.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

}
