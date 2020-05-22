<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LandingPageController extends AbstractController
{
    /**
     * @Route("/landing/page", name="landing_page")
     */
    public function index()
    {   $active = 0;
        return $this->render('landing_page/index.html.twig', [
            'controller_name' => 'LandingPageController', 'current' => $active ,
        ]);
    }
}
