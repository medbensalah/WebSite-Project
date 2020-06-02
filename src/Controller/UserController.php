<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Swift_SmtpTransport;
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
    public function addUser(EntityManagerInterface $manager, Request $request, Session $session)
    {
        $user = new User();
        $user->setVerified(false);
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);


        if ($form->isSubmitted() &&
            $form->isValid() &&
            ($request->request->get('terms') == 1) &&
            ($request->request->get('privacy') == 1) &&
            captchaverify($request->get('g-recaptcha-response'))) {
            $newuser = $request->request->get('user');
            $check = $request->request->get('passwordCheck');
            if ($newuser['password'] != $check) {

                $this->addFlash('alert', 'Password does not match');
                $this->redirectToRoute('user.form');
            }
            try {
                $session->set('user', $user);
                $manager->persist($user);
                $manager->flush();
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('alert', 'This email address already exists!');
                $this->redirectToRoute('user.form');
            }
            $this->sendVerificationEmail($session);
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
     * @Route("/verficationEmail", name="email" )
     * @param Session $session
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function sendVerificationEmail(Session $session)
    {
        $user = $session->get('user');
        $transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
            ->setUsername('taarafchi.no.reply@gmail.com')
            ->setPassword('taarafchi123');

        $mailer = new Swift_Mailer($transport);

        $message = (new \Swift_Message('Taarafchi verification'))
            ->setFrom('taarafchi@doNotReply.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig', [
                        'name' => $user->getName(),
                        'firstName' => $user->getFirstName(),
                        'id' => $user->getID()
                    ]
                ),
                'text/html'
            );

        $mailer->send($message);
        return $this->redirectToRoute('landing_page');
    }

    function captchaverify($recaptcha)
    {
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            "secret" => "6Ld9-f4UAAAAALopnvCMVDtIs11WxevF0TtuRuOB", "response" => $recaptcha));
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response);

        return $data->success;
    }

}
