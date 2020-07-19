<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    /**
     * @Route("/index/{sessionId}", name="application_menu")
     */
    public function renderMenu($sessionId)
    {
        return $this->render('menu/menu.html.twig', [
            'session' => $sessionId
        ]);


    }

}
