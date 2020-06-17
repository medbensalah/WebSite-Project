<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\User;
use DateTime;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index(Session $session)
    {
        if(!$session->get('admin')){
            return $this->redirectToRoute('landing_page');
        }
        $weekActivity = $this->getDoctrine()->getRepository(User::class)->findUsersPerWeek();
        $monthActivity = $this->getDoctrine()->getRepository(User::class)->findUsersPerMonth();
        $yearActivity = $this->getDoctrine()->getRepository(User::class)->findUsersPerYear();
        $usersToday = count($this->getDoctrine()->getRepository(User::class)->findBy([
            'active' => new DateTime(date('Y-m-d', strtotime('now')))
        ]));
        $users = count($this->getDoctrine()->getRepository(User::class)->findAll());
        $allUsers = $this->getDoctrine()->getRepository(User::class)->findBy([],[
            'verified' => 'ASC']
        );
        $jobs = $this->getDoctrine()->getRepository(Job::class)->findBy([],[
            'created' => 'ASC',
            'confirmed' => 'ASC'
        ]);
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'weekActivity' => $weekActivity,
            'monthActivity' => $monthActivity,
            'yearActivity' => $yearActivity,
            'usersToday' => $usersToday * 100 / $users,
            'userCountToday' => $usersToday,
            'users' => $users,
            'allUsers' => $allUsers,
            'jobs' => $jobs
        ]);
    }
}
