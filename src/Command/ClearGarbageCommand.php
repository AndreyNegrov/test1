<?php

namespace App\Command;

use App\Entity\Garbage;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TelegramBot\Api\BotApi;

class ClearGarbageCommand extends Command
{
    protected static $defaultName = 'clear:garbage';

    /**
     * @var EntityManager
     */
    private $em;

    protected function configure()
    {
        $this->setDescription('Clear garbage');
    }

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $bot = new BotApi('1284809420:AAHUU21Mv2HD9JUT4bH5eB-MUlJvuoG_F04');

            $garbage = $this->em->getRepository(Garbage::class)->findGarbage(5, null);

            /**@var Garbage $item **/
            foreach ($garbage as $item) {
                $bot->deleteMessage((int)$item->getTelegramId(), (int)$item->getMessageId());
                $this->em->remove($item);
                $this->em->flush();
            }

            file_put_contents('finish.log', 'finish success');
        } catch (\Throwable $e) {
            file_put_contents('error.log', $e->getMessage());
        }

        $io = new SymfonyStyle($input, $output);
        $io->success('Success');

        return 0;
    }
}
