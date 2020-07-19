<?php

namespace App\Command;

use App\Entity\Item;
use App\Entity\User;
use App\Entity\UserItem;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;
use TelegramBot\Api\HttpException;
use TelegramBot\Api\InvalidArgumentException;

class FilesForeach extends Command
{
    protected static $defaultName = 'files:foreach';

    /**
     * @var EntityManager
     */
    private $em;

    protected function configure()
    {
        $this->setDescription('change files size');
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
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ORMException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $files = scandir('public/words/pictures');

        foreach ($files as $file) {

            if ($file == '.' or $file == '..') {
                continue;
            }


            $picturePath = '/var/www/html/public/words/pictures/' . $file;
            $cardPicturePath = '/var/www/html/public/words/cards/' . $file;

            var_dump("convert $picturePath -strip -quality 30 $picturePath");
            var_dump("convert $cardPicturePath -strip -quality 30 $picturePath");

            exec("convert $picturePath -strip -quality 30 $picturePath");
            exec("convert $cardPicturePath -strip -quality 30 $cardPicturePath");

        }

//        var_dump($files[0]);

        return 0;
    }

}
