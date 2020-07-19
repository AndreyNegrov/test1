<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\User;
use App\Entity\UserItem;
use App\Service\Training\ComplexService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use TelegramBot\Api\BotApi;

class TrainingController extends AbstractController
{
    /**
     * @Route("/complex/{sessionId}", name="training_complex")
     *
     * @param string $sessionId
     * @param ComplexService $complexService
     * @return Response
     * @throws \Exception
     */
    public function complex($sessionId, ComplexService $complexService)
    {
        $data = $complexService->dataPreparing($sessionId);

        if ($data['empty']) {
            return $this->render('training/emptyTraining.html.twig', [
                'session' => $sessionId
            ]);
        }

        return $this->render('training/complex2.html.twig', $data);
    }


    /**
     * @Route("/repeat_data/{session}", name="training_repeat_data")
     *
     * @param string $session
     * @param ComplexService $complexService
     * @return Response
     * @throws \Exception
     */
    public function repeatData($session, ComplexService $complexService)
    {
        $data = $complexService->dataRepeatPreparing($session);
        return new JsonResponse($data);
    }

    /**
     * @Route("/training_data/{session}", name="training_data")
     *
     * @param string $session
     * @param ComplexService $complexService
     * @return Response
     * @throws \Exception
     */
    public function trainingsData($session, ComplexService $complexService)
    {
        $trainingData = $complexService->dataPreparing($session);
        $repeatData = $complexService->dataRepeatPreparing($session);
        return new JsonResponse([
            'training' => $trainingData,
            'repeat' => $repeatData
        ]);
    }

    /**
     * @Route("/finish", name="finish_trainig")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function finish(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $session = $request->get('session_id');
        /**@var $user User* */
        $user = $em->getRepository(User::class)->findOneBy([
            'userSession' => $session
        ]);

        $successItemsId = $request->get('success_items_id');

        $userItems = $em->getRepository(UserItem::class)->findBy([
            'user' => $user,
            'item' => explode(',', $successItemsId)
        ]);

        $bot = new BotApi('1284809420:AAHUU21Mv2HD9JUT4bH5eB-MUlJvuoG_F04');

        foreach ($userItems as $userItem) {
//            $bot->deleteMessage($userItem->getUser()->getTelegramId(), $userItem->getMessageId());
            $userItem->setStatus($userItem->getStatus() + 1);
        }

        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
