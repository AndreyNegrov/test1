<?php

namespace App\Controller;

use App\Entity\Garbage;
use App\Entity\User;
use App\Entity\UserItem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use Throwable;

class BotController extends AbstractController
{
    public function send()
    {
        $a = exec('php /var/www/html/bin/console send:item');
        return new Response($a);
    }

    public function webHooks(Request $request)
    {
        $bot = new BotApi('1284809420:AAHUU21Mv2HD9JUT4bH5eB-MUlJvuoG_F04');
        $em = $this->getDoctrine()->getManager();

        try {
            $content = json_decode($request->getContent(), true);

            $messageId = null;
            if (isset($content['message']['text']) and $content['message']['text'] == "🏋️ Тренировки‍") {

                $this->clearGarbage(0, $bot, $content['message']['chat']['id']);
                $bot->deleteMessage($content['message']['chat']['id'], $content['message']['message_id']);

                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                    [
                        [
                            ['text' => 'Начать тренировку', 'callback_game' => [
                                'game_short_name' => 'mr_teacher_train'
                            ]]
                        ]
                    ]
                );

                $messageId = $bot->sendGame($content['message']['chat']['id'], 'mr_teacher_train', false, null, $keyboard);

                $garbage = new Garbage();
                $garbage->setTelegramId($content['message']['chat']['id']);
                $garbage->setMessageId($messageId);
                $garbage->setDateAdded(new \DateTime());
                $em->persist($garbage);
                $em->flush();

            } elseif (isset($content['message']['text']) and $content['message']['text'] == "🧩\nМеню") {
                $this->clearGarbage(0, $bot, $content['message']['chat']['id']);
                $bot->deleteMessage($content['message']['chat']['id'], $content['message']['message_id']);

                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                    [
                        [
                            ['text' => 'Войти в меню', 'callback_game' => [
                                'game_short_name' => 'menu'
                            ]]
                        ]
                    ]
                );

                $messageId = $bot->sendGame($content['message']['chat']['id'], 'menu', false, null, $keyboard);

                $garbage = new Garbage();
                $garbage->setTelegramId($content['message']['chat']['id']);
                $garbage->setMessageId($messageId);
                $garbage->setDateAdded(new \DateTime());
                $em->persist($garbage);
                $em->flush();

            } elseif (isset($content['callback_query'])) {
                $this->sendGame($content, $bot);
            } elseif (isset($content['message']['text']) && $content['message']['text'] == '/start') {

                $bot->deleteMessage($content['message']['chat']['id'], $content['message']['message_id']);

                // Все что идет дальше, относится к отсылке /start пользователем

                $keyboard = [
                    ["🏋️ Тренировки‍", "🧩\nМеню", "📋\nПравила"]
                ];

                $reply_markup = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
                    $keyboard,
                    false,
                    true
                );

                $user = $em->getRepository(User::class)->findOneBy(["telegramId" => $content['message']['chat']['id']]);

                if ($user) {
//                    $bot->sendMessage($content['message']['chat']['id'], 'Мы снова рады видеть Вас', null, false, null, $reply_markup);
                    $user->setStatus(0);
                } else {

                    $user = new User();
                    $user->setTelegramId($content['message']['chat']['id']);
                    $user->setFirstName(isset($content['message']['from']['first_name']) ? $content['message']['from']['first_name'] : '');
                    $user->setLastName(isset($content['message']['from']['last_name']) ? $content['message']['from']['last_name'] : '');
                    $user->setLevel(2);
                    $user->setScore(0);
                    $user->setStatus(0);
                    $user->setLanguageCode('');
                    $user->setUserSession($this->generateRandomString());

                    $em->persist($user);
                    $em->flush();

                    for ($i = 0; $i <= 6; $i++) {
                        $item = $em->getRepository('App\Entity\Item')->findOneNoShowedByUser($user);

                        $messagePhoto = $bot->sendPhoto($user->getTelegramId(),
                            $item->getCard(),
                            sprintf('***%s*** `[%s]` - %s', $item->getEnglish(), $item->getTranscription(), $item->getWord()),
                            null,
                            $reply_markup,
                            false,
                            "markdown"
                        );

                        $userItem = new UserItem();
                        $userItem->setStatus(0);
                        $userItem->setUser($user);
                        $userItem->setItem($item);
                        $userItem->setMessageId($messagePhoto->getMessageId());

                        $em->persist($userItem);
                        $em->flush();
                    }

                }
            } else {
                $bot->deleteMessage($content['message']['chat']['id'], $content['message']['message_id']);
            }
            return new Response();
        } catch
        (Throwable $e) {
            $bot->sendMessage($content['message']['chat']['id'], $e->getMessage());
        }

        return new Response();
    }

    public function clearGarbage($minutes, BotApi $bot, $telegramId = null)
    {
        $em = $this->getDoctrine()->getManager();
        $garbage = $em->getRepository(Garbage::class)->findGarbage($minutes, $telegramId);

        /**@var Garbage $item **/
        foreach ($garbage as $item) {
            try {
                $mess = $bot->deleteMessage((int)$item->getTelegramId(), (int)$item->getMessageId());
            } catch (Throwable $e) {

            }
            $em->remove($item);
            $em->flush();
        }
    }

    private function sendGame($content, BotApi $bot)
    {
            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository(User::class)->findOneBy([
                'telegramId' => $content['callback_query']['from']['id']
            ]);

            file_put_contents('asasas.json', json_encode($content));

            if ($content['callback_query']['game_short_name'] == 'menu') {
                $bot->answerCallbackQuery($content['callback_query']['id'], null, false, 'https://mister-teacher.com/menu/index/' . $user->getUserSession());
                file_put_contents('cq.txt', $content['callback_query']['id']);
            } else {
                $bot->answerCallbackQuery($content['callback_query']['id'], null, false, 'https://mister-teacher.com/training/complex/' . $user->getUserSession());
            }


        return new Response();
    }

    function generateRandomString($length = 50)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


}
