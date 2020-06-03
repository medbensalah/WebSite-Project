<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\ReCaptchaValidator;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use function Composer\Autoload\includeFile;
use function Symfony\Component\String\u;


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
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param Session $session
     * @param ReCaptchaValidator $captcha
     */
    public function addUser(EntityManagerInterface $manager, Request $request, Session $session, ReCaptchaValidator $captcha)
    {
        $user = new User();
        $user->setVerified(false);
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);


        if ($form->isSubmitted() &&
        $form->isValid() &&
        ($request->request->get('terms') == 1) &&
        ($request->request->get('privacy') == 1)
            //&&
//        $captcha->captchaverify($request)
        ) {
            $newuser = $request->request->get('user');
            $check = $request->request->get('passwordCheck');
            if ($newuser['password'] != $check) {
                $this->addFlash('alert', 'Password does not match');
                return $this->redirectToRoute('user.form');
            }
            try {
                $session->set('user', $user);
                $manager->persist($user);
                $manager->flush();
                return $this->redirectToRoute('email');
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('alert', 'This email address already exists!');
                return $this->redirectToRoute('user.form');
            }
        }

        return $this->render('user/sign_up.html.twig', [
            'form' => $form->createView(),
            'current' => -1
        ]);
    }

    /**
     * @param $id
     * @Route("/activate/{id}", name="user.activate")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */

    public function activate($id, EntityManagerInterface $manager)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);
        $user->setVerified(true);
        $manager->flush();
        return $this->redirectToRoute('landing_page');
    }

    /**
     * @param $id
     * @Route("/delete/{id}", name="user.delete")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */

    public function delete($id, EntityManagerInterface $manager)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);
        $user->setVerified(true);
        $manager->remove($user);
        $manager->flush();
        return $this->redirectToRoute('landing_page');
    }

}

