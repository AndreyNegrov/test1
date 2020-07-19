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

class SetWebhookCommand extends Command
{
    protected static $defaultName = 'set:webhook';

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

            $bot->setWebhook('https://mister-teacher.com/hooks');
        } catch (\Throwable $e) {
            file_put_contents('error.log', $e->getMessage());
        }

        $io = new SymfonyStyle($input, $output);
        $io->success('Success');

        return 0;
    }
}
