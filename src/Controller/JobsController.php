<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Job;
use App\Entity\User;
use App\Form\JobType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobsController extends AbstractController
{
    /**
     * @Route("/jobs/add", name="job.add")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function index(Request $request, EntityManagerInterface $manager)
    {
        $myCookie = $request->cookies->get('mail');
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $myCookie
        ]);
        if(!$user){
            return $this->redirectToRoute('user.log_in');
        }
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);
        if ($form->isSubmitted() &&
            $form->isValid()) {
            if($form['image']->getData()) {
                $image = $form['image']->getData();
                $path = md5(uniqid()).$image->getClientOriginalName();
                $destination = __DIR__.'/../../public/img/Job/'.$form['categorie']->getData();
                try{
                    $image->move($destination, $path);
                    $job->setPhoto('../img/Profile/userProfileImages/'.$path);
                } catch(FileException $fe) {
                    echo $fe;
                }
            }
            $job->setConfirmed(false);
            $job->setUser($user);
            $job->setCreated(new \DateTime("now"));
            $manager->persist($job);
            $manager->flush();
            return $this->redirectToRoute('landing_page');
        }

        return $this->render('jobs/index.html.twig', [
            'controller_name' => 'JobsController',
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }
}
