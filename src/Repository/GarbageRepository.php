<?php

namespace App\Repository;

use App\Entity\Garbage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class GarbageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Garbage::class);
    }

    public function findGarbage($minutes, $telegramId)
    {
        $date = new \DateTime();
        $date->modify("-$minutes minutes");


        $request = $this->createQueryBuilder('garbage')
            ->andWhere('garbage.dateAdded < :date')
            ->setParameter(':date', $date);

        if ($telegramId) {
            $request
                ->andWhere('garbage.telegramId = :telegramId')
                ->setParameter(':telegramId', $telegramId);
        }

        return $request
                ->getQuery()
                ->getResult();
    }
}
