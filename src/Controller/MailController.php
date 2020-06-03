<?php

namespace App\Controller;

use Swift_Mailer;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{
    /**
     * @Route("/mail", name="mail")
     */
    public function index()
    {
        return $this->render('mail/index.html.twig', [
            'controller_name' => 'MailController',
        ]);
    }


    /**
     * @Route("/verification/mail",  name = "email")
     * @param Session $session
     * @return RedirectResponse
     */
    public function sendVerificationEmail(Session $session) {
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
}
