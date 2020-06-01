<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use function Composer\Autoload\includeFile;


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
    public function addUser(EntityManagerInterface $manager, Request $request) {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);


        if($form->isSubmitted() &&
            $form->isValid() &&
            ($request->request->get('terms') == 1) &&
            captchaverify($request->get('g-recaptcha-response'))) {
            dd($user);
        }

            return $this->render('user/sign_up.html.twig', [
            'form' => $form->createView(),
            'current' => -1
        ]);
    }
}
