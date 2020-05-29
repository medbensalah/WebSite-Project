<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categories;

class LandingPageController extends AbstractController
{
    /**
     * @Route("/landing/page", name="landing_page")
     */
    public function index()
    {
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();
        $active = 0;
        return $this->render('landing_page/index.html.twig', [
            'controller_name' => 'LandingPageController', 'current' => $active ,
            'categories' => $categories,
        ]);
    }
}
