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

class ACommand extends Command
{
    protected static $defaultName = 'get:word';

    /**
     * @var EntityManager
     */
    private $em;

    protected function configure()
    {
        $this->setDescription('Get word');
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
//        $litters = [
//            'q',
//            'h',
//            'p',
//            'q',
//            'm',
//            'k',
//            'd'
//        ];

        $litters = [
            '*',
            'm',
            'y',
            'j',
            'q',
            'f',
            'f'
        ];

        $middles = [

            'fix',
            'ajok',
            'costub',
            'bm',
            'soup',
            'boxt',
            'vogue',
            'd',
            'cuponumb',
            'p',
            'cave',
            'm',
            'ljump',
            'tp',
            'larray',
            'w',
            's',
            'drinkv',
            'dive',
            'e',
            'e',
            'n',
            'text',
            'colony',
            'w',
            'k',
            'dartedf',

            'dew',
            'r',
            'link',
            'plan',
            'v',
            'jrkt',
            'occurved',
            'c',
            'bguama',
            'xa',
            'o',
            'soupvpy',
            'tr',
            's',
            'oxeoe',
            'd',
            't',
            'fatbut',
            'n',
            'twice',
            'ijump',
            'dump',
            'vod',
            'xob',
            'm',
            'self',
            'k',
            'b',
            'o',
            'n',
            'y'
        ];

        $starts = [];
        $ends = [];

        foreach ($middles as $word) {
            $v = '';
            for ($i = 0; $i < strlen($word); $i++){
                $ends[] = $v . $word[$i];

                $v .= $word[$i];
            }
        }

        foreach ($middles as $word) {
            $v = '';
            for ($i = strlen($word) -1; $i >= 0; $i--){
                $starts[] = $word[$i] . $v;

                $v = $word[$i] . $v;
            }
        }


        $handle = fopen("words.txt", "r");

        $allWord = [];

        while (!feof($handle)) {
            $buffer = fgets($handle, 4096);
            $string = explode(" ", $buffer);


            foreach ($starts as $start) {
                $s = substr($string[0], 0, strlen($start));

                if ($start != $s or in_array($string[0], $allWord)) {
                    continue;
                }

                $wordWithout = substr($string[0], strlen($start));

                if ($this->check($wordWithout, $litters)) {
                    var_dump($this->check($wordWithout, $litters) . " - " . $string[0] . " - " . $start . " - " . $wordWithout);
                    $allWord []= $string[0];
                };
            }

            foreach ($ends as $end) {
                $s = substr($string[0], strlen($end));



                if ($end != $s or in_array($string[0], $allWord)) {
                    continue;
                }

                $wordWithout = substr($string[0], 0, strlen($string[0]) - strlen($end));

                if ($this->check($wordWithout, $litters)) {
                    var_dump($this->check($wordWithout, $litters) . " - " . $string[0] . " - " . $end . " - " . $wordWithout);
                    $allWord []= $string[0];
                };
            }

            foreach ($middles as $middle) {
                $s = stristr($string[0], $middle);


                if ($s == '' or in_array($string[0], $allWord)) {
                    continue;
                }


                $wordWithout = $this->str_replace_once($middle, '', $string[0]);

                if ($this->check($wordWithout, $litters)) {
                    var_dump($this->check($wordWithout, $litters) . " - " . $string[0] . " - " . $middle . " - " . $wordWithout);
                    $allWord []= $string[0];
                };
            }


        }

        return 0;
    }

    function str_replace_once($search, $replace, $text){
        $pos = strpos($text, $search);
        return $pos!==false ? substr_replace($text, $replace, $pos, strlen($search)) : $text;
    }

    function check($word, array $litters) {

        for ($i = 0; $i < strlen($word); $i++){

                if (!in_array($word[$i], $litters)) {

                    if (in_array('*', $litters)) {
                        foreach ($litters as $key => $litter) {
                            if ($litter == '*') {
                                unset($litters[$key]);
                            }
                        }
                        continue;
                    }

                    return false;
                }

                foreach ($litters as $key => $litter) {
                    if ($litter == $word[$i]) {
                        unset($litters[$key]);
                    }
                }
        }

        return strlen($word);

    }

}
