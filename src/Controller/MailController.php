<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\User;
use Swift_Mailer;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{

    /**
     * @Route("/verification/mail",  name = "email")
     * @param Session $session
     * @return RedirectResponse
     */
    public function sendVerificationEmail(Session $session) {
        $user = $session->get('signUser');
        $transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
            ->setUsername('taarafchi.no.reply@gmail.com')
            ->setPassword('taarafchi123');

        $mailer = new Swift_Mailer($transport);
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();

        $message = (new \Swift_Message('Taarafchi verification'))
            ->setFrom('taarafchi@doNotReply.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig', [
                        'name' => $user->getNom(),
                        'firstName' => $user->getPrenom(),
                        'id' => $user->getID(),
                        'categories' => $categories
                    ]
                ),
                'text/html'
            );

        $mailer->send($message);
        return $this->redirectToRoute('user.confirm');
    }
    /**
     * @Route("/restore/mail",  name = "email.restore")
     * @param Session $session
     * @return RedirectResponse
     */
    public function sendRestoreEmail(Session $session) {
        $email = $session->get('email');
        $transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
            ->setUsername('taarafchi.no.reply@gmail.com')
            ->setPassword('taarafchi123');

        $mailer = new Swift_Mailer($transport);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $email
        ]);
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();

        $message = (new \Swift_Message('Taarafchi restore account'))
            ->setFrom('taarafchi@doNotReply.com')
            ->setTo($email)
            ->setBody(
                $this->renderView(
                    'emails/restore.html.twig', [
                        'name' => $user->getNom(),
                        'firstName' => $user->getPrenom(),
                        'id' => $user->getID(),
                        'categories' => $categories
                    ]
                ),
                'text/html'
            );

        $mailer->send($message);
        return $this->redirectToRoute('user.confirm');
    }

    /**
     * @Route("/mail/confirm", name="user.confirm")
     */
    public function confirm(){
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();

        return $this->render('user/confirmAccount.html.twig', [
            'categories' => $categories
        ]);
    }
}
