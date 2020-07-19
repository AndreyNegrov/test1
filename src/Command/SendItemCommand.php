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

class SendItemCommand extends Command
{
    protected static $defaultName = 'send:item';

    /**
     * @var EntityManager
     */
    private $em;

    protected function configure()
    {
        $this->setDescription('Send item');
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
        $bot = new BotApi('1284809420:AAHUU21Mv2HD9JUT4bH5eB-MUlJvuoG_F04');

//        $bot->sendGame('495919958', 'mr_teacher_train');
//        $bot->sendGame('792843444', 'mr_teacher_train');
//        $bot->sendGame('1043908814', 'mr_teacher_train');
//        $bot->deleteMessage('495919958', '2569');

        $keyboard = [["🏋️ Тренировки‍", "🧩" . PHP_EOL . "Меню", "📋\nПравила"]];
        $reply_markup = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
            $keyboard,
            false,
            true
        );

        $users = $this->em->getRepository(User::class)->findBy(['status' => 0]);

        /**@var $user User */
        foreach ($users as $user) {
            /**@var $item Item* */
            $item = $this->em->getRepository('App\Entity\Item')->findOneNoShowedByUser($user);
            if (!$item) {
                continue;
            }

            try {
                $messagePhoto = $bot->sendPhoto($user->getTelegramId(),
                    $item->getCard(),
                    sprintf('***%s*** `[%s]` - %s', $item->getEnglish(), $item->getTranscription(), $item->getWord()),
                    null,
                    $reply_markup,
                    false,
                    "markdown"
                );
            } catch (HttpException $e) {
                if ($e->getMessage() === 'Forbidden: bot was blocked by the user') {
                    $user->setStatus(1);
                    $this->em->flush();
                    continue;
                } else {
                    throw $e;
                }
            }

            $userItem = new UserItem();
            $userItem->setStatus(0);
            $userItem->setUser($user);
            $userItem->setItem($item);
            $userItem->setMessageId($messagePhoto->getMessageId());

            $this->em->persist($userItem);
            $this->em->flush();
        }

        $io = new SymfonyStyle($input, $output);
        $io->success('Success');

        return 0;
    }
}
