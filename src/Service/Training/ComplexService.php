<?php

namespace App\Service\Training;

use App\Entity\Item;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class ComplexService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $session
     * @return array
     * @throws \Exception
     */
    public function dataPreparing(string $session)
    {
        /**@var $user User* */
        $user = $this->em->getRepository(User::class)->findOneBy([
            'userSession' => $session
        ]);

        if (!$user) {
            throw new \Exception('Authorization failed');
        }

        $items = $this->em->getRepository(Item::class)->findByUserRandomNotStudiedWords($user, 7);

        return [
            'session' => $session,
            'items' => json_encode($items),
            'empty' => count($items) < 7
        ];
    }

    /**
     * @param string $session
     * @return array
     */
    public function dataRepeatPreparing(string $session)
    {
        /**@var $user User* */
        $user = $this->em->getRepository(User::class)->findOneBy([
            'userSession' => $session
        ]);

        if (!$user) {
            throw new \Exception('Authorization failed');
        }

        $items = $this->em->getRepository(Item::class)->findByUserRandomNotRepeatedWords($user, 7);

        return [
            'session' => $session,
            'items' => $items,
            'empty' => count($items) < 5,
            'trainings' => [
                'YesNoTraining',

                 'ListeningEnglishContextTraining',
                'ListeningHardTraining', 'RussianSynonymTraining', 'ListeningTranslateTraining',

                'RussianContextTraining',

            'WriteRussianTraining', 'WriteEnglishTraining'
            ],
        ];
    }

}
