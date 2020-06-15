<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Gouvernorat;
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
use Symfony\Component\HttpFoundation\Session\Session;
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
                    $job->setPhoto('../img/Job/'.$form['categorie']->getData().'/'.$path);
                } catch(FileException $fe) {
                    $this->addFlash('file_error',"erreur d'envoie de l'image, veuileez réessayer.");
                    $this->redirectToRoute('job.add');
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

    /**
     * @Route("job/delete/{id}", name="job.delete")
     * @param Job $job
     * @param EntityManagerInterface $manager
     */
    public function deleteJob(Job $job, EntityManagerInterface $manager, Request $request){
        $manager->remove($job);
        $manager->flush();
        $userID = $request->query->get('userID');
        return $this->redirectToRoute('jobs.edit.user', [
            'id' => $userID
        ]);
    }

    /**
     * @Route("/jobs/surf", name="jobs.surf")
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function surf(Request $request, Session $session) {
        $myCookie = $request->cookies->get('mail');
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $myCookie
        ]);
        if($user){
            $session->set('user', $user);
        }
        $page = $request->query->get('page') ?? 1;
        $repository = $this->getDoctrine()->getRepository(Job::class);
        $nbEnregistrements = $repository->count(array());
        $nbpage = ($nbEnregistrements % 15) ? $nbEnregistrements / 15 + 1 : $nbEnregistrements / 15;
//        dd($page);
        $jobs = $repository->findAllJobs(($page - 1) * 15, 15);
        return $this->render('jobs/surfJobs.html.twig', [
            'jobs' => $jobs,
            'nbPages' => $nbpage,
            'currentPage' => $page,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("job/searchForm", name="job.search.form")
     */
    public function search(){
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();
        $gouvernorats = $this->getDoctrine()->getRepository(Gouvernorat::class)->findAll();
        return $this->render('jobs/jobs_search.html.twig', [
            'categories' => $categories,
            'current' => 2,
            'gouvernorats' => $gouvernorats
        ]);
    }


    /**
     * @Route("/jobs/search", name="jobs.search")
     */
    public function seachJob(Request $request, Session $session) {
        $myCookie = $request->cookies->get('mail');
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $myCookie
        ]);
        if($user){
            $session->set('user', $user);
        }
        $govs = $request->query->get('gov');
        $cats = $request->query->get('cat');
        $desc = $request->query->get('description');

        $page = $request->query->get('page') ?? 1;
        $repository = $this->getDoctrine()->getRepository(Job::class);
        $jobs = $repository->findCustomJob($govs,$cats,$desc,($page - 1) * 15);
        $nbEnregistrements = count($repository->findCustomJob($govs,$cats,$desc,0, 0));
        $nbpage = ($nbEnregistrements % 15) ? $nbEnregistrements / 15 + 1 : $nbEnregistrements / 15;
//dd($nbpage);
        return $this->render('jobs/searchJobs.html.twig', [
            'jobs' => $jobs,
            'nbPages' => $nbpage,
            'currentPage' => $page,
            'categories' => $categories,
            'govs' => $govs,
            'cats' => $cats,
            'desc' => $desc
        ]);
    }

    /**
     * @Route("/jobs/searchCat/{id?1}", name="jobs.search.cat")
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function seachJobCat(Categories $cat, Request $request, Session $session) {
        $myCookie = $request->cookies->get('mail');
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $myCookie
        ]);
        if($user){
            $session->set('user', $user);
        }
        $page = $request->query->get('page') ?? 1;
        $repository = $this->getDoctrine()->getRepository(Job::class);
        $jobs = $repository->findCatJob($cat->getId(),($page - 1) * 15);
        $nbEnregistrements = count($repository->findUserJob($cat->getId(),0, 0));

        $nbpage = ($nbEnregistrements % 15) ? $nbEnregistrements / 15 + 1 : $nbEnregistrements / 15;

        return $this->render('jobs/searchCatJobs.html.twig', [
            'jobs' => $jobs,
            'nbPages' => $nbpage,
            'currentPage' => $page,
            'categories' => $categories,
            'cat' => $cat
        ]);
    }

    /**
     * @Route("/jobs/searchUser/{id?1}", name="jobs.edit.user")
     * @param User $u
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function seachJobUser(User $u, Request $request, Session $session) {
        $myCookie = $request->cookies->get('mail');
        $categories=$this->getDoctrine()->getRepository(Categories::class)->findAll();
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $myCookie
        ]);
        if($user){
            $session->set('user', $user);
            if($u->getId() == $user->getId())
                $show = 1;
            else
                $show = 0;
        }
        $page = $request->query->get('page') ?? 1;
        $repository = $this->getDoctrine()->getRepository(Job::class);
        $jobs = $repository->findUserJob($u->getId(),($page - 1) * 15);
        $nbEnregistrements = count($repository->findUserJob($u->getId(),0, 0));
        $nbpage = ($nbEnregistrements % 15) ? $nbEnregistrements / 15 + 1 : $nbEnregistrements / 15;

        return $this->render('jobs/userJobs.html.twig', [
            'jobs' => $jobs,
            'nbPages' => $nbpage,
            'currentPage' => $page,
            'categories' => $categories,
            'show' => $show,
            'u' => $u
        ]);
    }

}
