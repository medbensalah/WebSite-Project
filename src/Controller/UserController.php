<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/sign_up", name="sign_up")
     */
    public function index()
    {
        return $this->render('user/sign_up.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/addUserForm", name="user.form")
     */
    public function addUser(EntityManagerInterface $manager) {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        return $this->render('user/sign_up.html.twig', [
            'form' => $form->createView(),
            'current' => -1
        ]);
    }
}
