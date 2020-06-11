<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\ReCaptchaValidator;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
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

        $myCookie = $request->cookies->get('mail');
//        if ()


        $user = new User();
        $user->setVerified(false);
        $user->setPhoto('/img/Profile/unknown.png');
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);


        $session->set('user', $user);
        if ($form->isSubmitted() &&
        $form->isValid() &&
        ($request->request->get('terms') == 1) &&
        ($request->request->get('privacy') == 1) &&
        $captcha->captchaverify($request)
        ) {
            $newuser = $request->request->get('user');
            $check = $request->request->get('passwordCheck');
            if ($newuser['motDePasse'] != $check) {
                $this->addFlash('alert', 'Password does not match');
                return $this->redirectToRoute('user.form');
            }
            try {
                $manager->persist($user);
                $manager->flush();
                return $this->redirectToRoute('email');
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('alert', 'This email address already exists!');
                return $this->redirectToRoute('user.form');
            }
        }
        else {
            if ($form->isSubmitted() &&
                !(($request->request->get('terms') == 1) &&
                ($request->request->get('privacy') == 1))) {

                $this->addFlash('required', "In order to proceed with the sign up you need to agree to our terms of use and privacy policy");
            }
            if ($form->isSubmitted() &&
                !$captcha->captchaverify($request)) {
                $this->addFlash('required', "In order to proceed with the sign up you need to confirm the reCAPTCHA");
            }
            return $this->render('user/sign_up.html.twig', [
                'form' => $form->createView(),
                'current' => -1
            ]);
        }
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

    /**
     * @Route("/log_in", name="log_in")
     * @return Response
     */
    public function login()
    {
        return $this->render('user/login.html.twig', [
            'current' => -1
        ]);
    }

    /**
     * @Route("/check/log_in", name="user.log_in")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Session $session
     * @return Response
     */
    public function checkLogin(Request $request, EntityManagerInterface $manager, Session $session)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy([
            'email' => $request->request->get('email'),
            'motDePasse' => $request->request->get('password')
        ]);
        if(!$user) {
            $this->addFlash('login_error', 'Veuillez verifier vos credentials.');

            return $this->render('user/login.html.twig', [
                'current' => -1
            ]);
        }
        else {
            $request->cookies->set('email', $user->getEmail());

            $cookie = new Cookie(
                'mail', $user->getEmail() ,
                time() + ( 2 * 365 * 24 * 60 * 60) ,
                '/'
            );
            $res = new Response();
            $res->headers->setCookie( $cookie );
            $res->send();
//            dd($myCookie = $request->cookies->get('mail'));
            $session->set('user', $user);

            return $this->redirectToRoute('landing_page');
        }

    }

}

