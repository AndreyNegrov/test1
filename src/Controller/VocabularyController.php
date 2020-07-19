<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class VocabularyController extends AbstractController
{
    /**
     * @Route("/index/{sessionId}", name="application_vocabulary")
     */
    public function renderVocabulary($sessionId)
    {
        $em = $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->getUserWithItemsBySession($sessionId);

        return $this->render('vocabulary/vocabulary.html.twig', [
            'session' => $sessionId,
            'user' => $user
        ]);
    }

    /**
     * @Route("/move/{session}/{id}/{status}", name="move_word_vocabulary")
     */
    public function moveWordVocabulary($session, $id, $status)
    {
        $em = $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->findOneBy(['userSession' => $session]);

        $userItem = $em->getRepository(UserItem::class)->find($id);

        if ($user->getId() === $userItem->getUser()->getId()) {
            $userItem->setStatus($status);
            $em->flush();
        }

        return new JsonResponse([
            'status' => 'success'
        ]);
    }

}
