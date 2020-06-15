<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Gouvernorat;
use App\Entity\User;
use App\Form\AlterUserType;
use App\Form\UserType;
use App\Service\ReCaptchaValidator;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Extra\String\StringExtension;

class UserController extends AbstractController
{
    /**
     * @Route("/sign_up", name="sign_up")
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    /**
     * @Route("/addUserForm", name="user.form")
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param Session $session
     * @param ReCaptchaValidator $captcha
     * @return RedirectResponse|Response
     */
    public function addUser(EntityManagerInterface $manager, Request $request, Session $session, ReCaptchaValidator $captcha)
    {
        $myCookie = $request->cookies->get('mail');
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $myCookie
        ]);
        if($user){
            $session->set('user', $user);
            return $this->redirectToRoute('landing_page');
        }
        $user = new User();
        $user->setVerified(false);
        $user->setPhoto('/img/Profile/unknown.png');
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
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
                $user->setId();
                $session->set('signUser', $user);
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
            $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();

            return $this->render('user/sign_up.html.twig', [
                'form' => $form->createView(),
                'current' => -1,
                'categories' => $categories
            ]);
        }
    }

    /**
     * @param $id
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     * @Route("/activate/{id}", name="user.activate")
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
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     * @Route("/delete/{id}", name="user.delete")
     */

    public function delete($id, EntityManagerInterface $manager)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);
        if ($user) {
            $user->setVerified(true);
            $manager->remove($user);
            $manager->flush();
        }
        return $this->redirectToRoute('landing_page');
    }

    /**
     * @Route("/log_in", name="log_in")
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function login(Request $request, Session $session)
    {
        $myCookie = $request->cookies->get('mail');
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $myCookie
        ]);
        if($user){
            $session->set('user', $user);
            return $this->redirectToRoute('landing_page');
        }
        return $this->render('user/login.html.twig', [
            'current' => -1,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/check/log_in", name="user.log_in")
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function checkLogin(Request $request, Session $session, EntityManagerInterface $manager)
    {
        $myCookie = $request->cookies->get('mail');
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $myCookie
        ]);
        if($user){
            $session->set('user', $user);
            return $this->redirectToRoute('landing_page');
        }
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy([
            'email' => $request->request->get('email'),
            'motDePasse' => $request->request->get('password')
        ]);
//        dd($user);
        if(!$user) {
            $this->addFlash('login_error', 'Veuillez verifier vos credentials.');

            return $this->render('user/login.html.twig', [
                'current' => -1,
                'categories' => $categories
            ]);
        }
        else {
            if($user->getVerified()==0){
                return $this->redirectToRoute('user.confirm');
            }

            $request->cookies->set('email', $user->getEmail());

            $cookie = new Cookie(
                'mail', $user->getEmail() ,
                time() + (365 * 24 * 60 * 60) ,
                '/'
            );
            $res = new Response();
            $res->headers->setCookie( $cookie );
            $res->send();
            $user->setActive(new \DateTime("now"));
            $manager->persist($user);
            $manager->flush();
            $session->set('user', $user);

            return $this->redirectToRoute('landing_page');
        }
    }

    /**
     * @Route("/disconnect", name="user.log_out")
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse
     */
    public function disconnect(Request $request, Session $session) {
        $response = new Response();
        $response->headers->clearCookie('mail');
        $response->send();
        $session->remove('user');
        return $this->redirectToRoute('landing_page');
    }

    /**
     * @Route("/restore", name="user.restore")
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    public function restore(Request $request, Session $session){
        $myCookie = $request->cookies->get('mail');
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $myCookie
        ]);
        if($user){
            $session->set('user', $user);
            return $this->redirectToRoute('landing_page');
        }
        return $this->render('user/restore.html.twig', [
            'current' => -1,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/check/restore", name="user.check.restore")
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    public function checkRestore(Request $request, Session $session) {
        $myCookie = $request->cookies->get('mail');
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $myCookie
        ]);
        if($user){
            $session->set('user', $user);
            return $this->redirectToRoute('landing_page');
        }
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy([
            'email' => $request->request->get('email')
        ]);
        if(!$user) {
            $this->addFlash('restore_error', 'Aucun compte correspondant');

            return $this->render('user/restore.html.twig', [
                'current' => -1,
                'categories' => $categories
            ]);
        }
        else {
            $session->set('email',$user->getEmail());
            return $this->redirectToRoute('email.restore');
        }
    }

    /**
     * @Route("/restore/{id}", name="user.change.password")
     * @param $id
     * @return Response
     */
    public function changePassword($id){
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();

        return $this->render('user/changePassword.html.twig', [
            'current' => -1,
            'categories' => $categories,
            'id' => $id
        ]);
    }

    /**
     * @Route("/restore/password/check/{id}", name="user.change.password.check")
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function changePasswordCheck($id, Request $request, EntityManagerInterface $manager) {
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();

        if ($request->request->get('password')==$request->request->get('password_check')) {
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);
            $user->setMotDePasse($request->request->get('password'));
            $manager->flush();

            return $this->render('user/login.html.twig', [
                'current' => -1,
                'categories' => $categories
            ]);
        }
        $this->addFlash('restore_error','Mot de passe non valide');
        return $this->render('user/changePassword.html.twig', [
            'current' => -1,
            'categories' => $categories,
            'id' => $id
        ]);
    }

    /**
     * @Route("/alterUserForm", name="user.alter")
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param Session $session
     * @param ReCaptchaValidator $captcha
     * @return RedirectResponse|Response
     */
    public function alterUser(EntityManagerInterface $manager, Request $request, Session $session)
    {
        $myCookie = $request->cookies->get('mail');
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $myCookie
        ]);

        $form = $this->createForm(AlterUserType::class, $user);
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();

        $form->handleRequest($request);
        if ($form->isSubmitted() &&
            $form->isValid()) {
            if($form['image']->getData()) {
                $image = $form['image']->getData();
                $path = md5(uniqid()).$image->getClientOriginalName();
                $destination = __DIR__.'/../../public/img/Profile/userProfileImages';
                try{
                    $image->move($destination, $path);
                    $user->setPhoto('../img/Profile/userProfileImages/'.$path);
                } catch(FileException $fe) {
                    $this->addFlash('file_error',"erreur d'envoie de l'image, veuileez réessayer.");

                    return $this->render('user/alter_user.html.twig', [
                        'form' => $form->createView(),
                        'current' => -1,
                        'categories' => $categories
                    ]);
                }
            }
            $session->set('user', $user);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('landing_page');
        }
        else {
            return $this->render('user/alter_user.html.twig', [
                'form' => $form->createView(),
                'current' => -1,
                'categories' => $categories
            ]);
        }
    }

    /**
     * @Route("/profile/{id}", name="user.profile")
     * @param User $user
     * @return Response
     */

    public function profile(User $user) {
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();
        return $this->render('user/profile.html.twig', [
            'current' => -1,
            'profile' => $user,
            'categories' => $categories
        ]);
    }
}

