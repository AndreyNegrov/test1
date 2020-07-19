<?php

namespace App\Controller;

use App\Entity\Item;
use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;

class AddingWordsController extends AbstractController
{
    /**
     * @Route("/index", name="adding_words")
     */
    public function renderAddingWords()
    {
        $em = $this->getDoctrine()->getManager();
        $items = $em->getRepository(Item::class)->findBy([], ["id" => "desc"]);
        return $this->render('adding_words/adding_words.html.twig', ["items" => $items]);
    }

    /**
     * @Route("/getWordInfo/{word}", name="get_word_info")
     *
     * @param $word
     * @return JsonResponse
     * @throws ApiException
     * @throws ValidationException
     */
    public function getWordInfo($word)
    {
        $this->downloadVoice($word);
        $textInfo = $this->getTextInfo($word);
        return new JsonResponse([
            'status' => 'success',
            'data' => $textInfo
        ]);
    }

    /**
     * @Route("/downloadImage", name="get_image")
     * @param Request $request
     * @return JsonResponse
     */
    public function downloadImage(Request $request)
    {
        $url = $request->get('url');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($ch);
        curl_close($ch);

        file_put_contents('words/tmp/abc.jpg', $content);
        return new JsonResponse($url);
    }


    /**
     * @Route("/createWord", name="create_word")
     */
    public function createWord(Request $request)
    {
        $name = uniqid();

        $picturePath = 'words/pictures/' . $name . '.jpg';
        $cardPicturePath = 'words/cards/' . $name . '.jpg';

        $this->base64ToJpeg($request->get('pictures'), $picturePath);
        $this->base64ToJpeg($request->get('card'), $cardPicturePath);

        $em = $this->getDoctrine()->getManager();
        $item = new Item();

        $item->setWord(trim(lcfirst($request->get('translate'))));
        $item->setEnglish(trim(strtoupper($request->get('english-word'))));
        $item->setPicture('https://mister-teacher.com/words/pictures/' . $name . '.jpg');
        $item->setCard('https://mister-teacher.com/words/cards/' . $name . '.jpg');
        $item->setTranscription(trim($request->get('transcription')));
        $item->setStatus(0);

        $item->setGlobalUser($this->getUser());
        $item->setDateAdded(new \DateTime());

        $additionalInfo = [];

        if ($request->get('explain')) {
            $additionalInfo['explain'] = $request->get('explain');
        }

        if ($request->get('explain-english')) {
            $additionalInfo['explain-english'] = $request->get('explain-english');
        }


        if ($request->get('russian-synonyms')) {
            $additionalInfo['russian-synonyms'] = $request->get('russian-synonyms');
        }

        if ($request->get('english-synonyms')) {
            $additionalInfo['english-synonyms'] = $request->get('english-synonyms');
        }

        if ($request->get('english-examples')) {
            $additionalInfo['examples'] = [];
            for ($i = 0; $i < count($request->get('english-examples')); $i++) {
                $this->downloadVoice($request->get('english-examples')[$i]);
                $additionalInfo['examples'][$i]['english'] = $request->get('english-examples')[$i];
                $additionalInfo['examples'][$i]['russian'] = $request->get('russian-examples')[$i];
            }
        }

        $item->setAdditionInfo(json_encode($additionalInfo));

        $em->persist($item);
        $em->flush();

//        $this->sendAfterCreating($item);

               exec("convert $picturePath -strip -quality 30 $picturePath");
        $res = exec("convert $cardPicturePath -strip -quality 30 $cardPicturePath");

        return $this->redirectToRoute('adding_words');
    }

    /**
     * @Route("/removeWord", name="remove_word")
     */
    public function removeWord(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /**@var Item $item **/
        $item = $em->getRepository(Item::class)->find($request->get('id'));

        foreach ($item->getUserItems() as $ua) {
            $em->remove($ua);
        }

        $em->remove($item);
        $em->flush();

        return $this->redirectToRoute('adding_words');
    }

    /**
     * @param $base64String
     * @param $outputFile
     * @return mixed
     */
    function base64ToJpeg($base64String, $outputFile)
    {
        $ifp = fopen($outputFile, 'wb');
        $data = explode(',', $base64String);
        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);
        return $outputFile;
    }

    /**
     * @param string $word
     * @return void
     * @throws ValidationException
     * @throws ApiException
     */
    private function downloadVoice($word)
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/google_cred.json');
        $client = new TextToSpeechClient();
        $input_text = (new SynthesisInput())
            ->setText($word);
        $voice = (new VoiceSelectionParams())
            ->setLanguageCode('en-US')
            ->setSsmlGender(SsmlVoiceGender::FEMALE);
        $audioConfig = (new AudioConfig())
            ->setAudioEncoding(AudioEncoding::MP3);
        $response = $client->synthesizeSpeech($input_text, $voice, $audioConfig);
        $audioContent = $response->getAudioContent();
        file_put_contents("words/voices/$word.mp3", $audioContent);
    }

    /**
     * @param $word
     * @return bool|string
     */
    private function getTextInfo($word)
    {
        $url = "https://dictionary.yandex.net/api/v1/dicservice.json/lookup?key=dict.1.1.20200212T205104Z.d3d65be9bda92387.745b7c996fd43e3d603e3b33e4e66a4d41eaf10e&lang=en-ru&text=$word";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        return curl_exec($ch);
    }

    /**
     * @param Item $item
     * @throws Exception
     * @throws InvalidArgumentException
     */
    private function sendAfterCreating(Item $item)
    {
        $bot = new BotApi('1284809420:AAHUU21Mv2HD9JUT4bH5eB-MUlJvuoG_F04');

//        $bot->sendPhoto(495919958,
//            $item->getCard(),
//            sprintf('***%s*** `[%s]` - %s', $item->getEnglish(), $item->getTranscription(), $item->getWord()),
//            null,
//            null,
//            false,
//            "markdown"
//        );

        $bot->sendPhoto(495919958,
            $item->getCard(),
            sprintf('***%s*** `[%s]` - %s', $item->getEnglish(), $item->getTranscription(), $item->getWord()),
            null,
            null,
            false,
            "markdown"
        );
    }

}
