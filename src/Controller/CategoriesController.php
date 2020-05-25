<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    /**
     * @Route("/categories", name="categories")
     */
    public function index()
    {
        $active = 1;
        return $this->render('categories/index.html.twig', [
            'controller_name' => 'CategoriesController', 'current' => $active ,
        ]);
    }
}
