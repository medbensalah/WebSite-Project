<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categories;

class LandingPageController extends AbstractController
{
    /**
     * @Route("/landing/page", name="landing_page")
     */
    public function index(Request $request, Session $session)
    {
//        $response = new Response();
//        $response->headers->clearCookie('mail');
//        $response->send();
//        $session->remove('user');
        $myCookie = $request->cookies->get('mail');
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $myCookie
        ]);
        if($user){
            $session->set('user', $user);
        }
//        dd($user);
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();
        return $this->render('landing_page/index.html.twig', [
            'controller_name' => 'LandingPageController',
            'current' => 0 ,
            'categories' => $categories
        ]);
    }
}
