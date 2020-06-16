<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $weekActivity = $this->getDoctrine()->getRepository(User::class)->findUsersPerWeek();
        $monthActivity = $this->getDoctrine()->getRepository(User::class)->findUsersPerMonth();
        $yearActivity = $this->getDoctrine()->getRepository(User::class)->findUsersPerYear();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'weekActivity' => $weekActivity,
            'monthActivity' => $monthActivity,
            'yearActivity' => $yearActivity
        ]);
    }
}
