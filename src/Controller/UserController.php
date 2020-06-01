<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
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
    public function addUser(EntityManagerInterface $manager, Request $request, Session $session) {
        $user = new User();
        $user->setVerified(false);
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

//
//        if($form->isSubmitted() &&
//            $form->isValid() &&
//            ($request->request->get('terms') == 1) &&
//            captchaverify($request->get('g-recaptcha-response'))) {
//            dd($session->get('user'));
//        }
        if($form->isSubmitted()) {
            $session->set('user', $user);
            $manager->persist($user);
            $manager->flush();
            $this->redirectToRoute('email');
        }

        return $this->render('user/sign_up.html.twig', [
            'form' => $form->createView(),
            'current' => -1
        ]);
    }

    /**
     * @Route("/verficationEmail", name="email" )
     * @param Session $session
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function sendVerificationEmail(Session $session,  \Swift_Mailer $mailer) {
        $user = $session->get('user');
        $message = (new \Swift_Message('Taarafchi verification'))
            ->setFrom('taarafchi@doNotRespond.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig', [
                        'name' => $user->getName(),
                        'firstName' => $user->getFirstName()
                    ]
                ),
                'text/html'
            )
        ;

        $mailer->send($message);

        return $this->redirectToRoute('landing_page');
    }

    function captchaverify($recaptcha) {
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            "secret"=>"6Ld9-f4UAAAAALopnvCMVDtIs11WxevF0TtuRuOB","response"=>$recaptcha));
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response);

        return $data->success;
    }

}
