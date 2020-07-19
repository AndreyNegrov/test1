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

class EditWordsController extends AbstractController
{
    /**
     * @Route("/edit/{itemId}", name="render_edit_word")
     */
    public function renderEditWord($itemId)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Item::class)->find($itemId);
        return $this->render('adding_words/edit_words.html.twig', [
            'item' => $item,
            'addition_info' => json_decode($item->getAdditionInfo(), 1)
        ]);
    }

    /**
     * @Route("/editWord", name="edit_word")
     */
    public function editWord(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $item = $em->getRepository(Item::class)->find($request->get('id'));

        $additionalInfo = [];

        $additionalInfo['explain'] = $request->get('explain');
        $additionalInfo['explain-english'] = $request->get('explain-english');
        $additionalInfo['russian-synonyms'] = $request->get('russian-synonyms');
        $additionalInfo['english-synonyms'] = $request->get('english-synonyms');

        $additionalInfo['examples'] = [];
        for ($i = 0; $i < count($request->get('english-examples')); $i++) {
            $this->downloadVoice($request->get('english-examples')[$i]);
            $additionalInfo['examples'][$i]['english'] = $request->get('english-examples')[$i];
            $additionalInfo['examples'][$i]['russian'] = $request->get('russian-examples')[$i];
        }

        $item->setAdditionInfo(json_encode($additionalInfo));
        $item->setUserEdited($this->getUser());
        $item->setDateEdited(new \DateTime());

        $em->flush();

        return $this->render('adding_words/edited.html.twig');
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
     * @Route("/block/{itemId}", name="block_word")
     */
    public function blockWord($itemId)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Item::class)->find($itemId);
        $item->setStatus(true);
        $em->flush();

        return new JsonResponse([
            'success' => true
        ]);
    }

    /**
     * @Route("/unblock/{itemId}", name="unblock_word")
     */
    public function unblockWord($itemId)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Item::class)->find($itemId);
        $item->setStatus(false);
        $em->flush();

        return new JsonResponse([
            'success' => true
        ]);
    }

}
