<?php


namespace App\Controller;

use App\Entity\Domain;
use App\Entity\Project;
use App\Entity\User;
use App\Form\ProjectFormType;
use App\Form\addUsersFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("main/projects", name="app_addProject")
     */
    public function new(Request $request): Response
    {
        if ($this->isGranted("IS_AUTHENTICATED_FULLY")) {

            $currentWorkspace = $this->session->get('currentWorkspace');
            $domain = $this->getDoctrine()->getRepository(Domain::class)->find($currentWorkspace);
            $project = new Project();

            $form = $this->createForm(ProjectFormType::Class, $project);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $project->setDomain($domain);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($project);
                $entityManager->flush();

                return $this->redirectToRoute('app_addProject');
            }
            return $this->render('projects/project_form.html.twig', ['form' => $form->createView()]);
        } else
            return $this->redirectToRoute("app_welcome");

    }

    /**
     * @Route("main/projects", name="app_displayProjects")
     */
    public function showAction()
    {
        if ($this->isGranted("IS_AUTHENTICATED_FULLY")) {
            $currentWorkspace = $this->session->get('currentWorkspace');

            $projects = $this->getDoctrine()
                ->getRepository(Project::class)
                ->findByWorkspace($currentWorkspace);

            return $this->render('/projects/show_projects.html.twig', array('projects' => $projects));
        } else {
            return $this->redirectToRoute("app_main");
        }
    }

    /**
     * @Route("projects/select/{currentProject}/{currentProjectName}")
     */
    public function select($currentProject, $currentProjectName)
    {


        $this->session->set('currentProject', $currentProject);
        $this->session->set('currentProjectName', $currentProjectName);
        return $this->redirectToRoute("app_displayProjects");
    }


        /**
         * @Route("main/projects/addUsers", name="app_addUsers")
         */
        public function setUsers(Request $request): Response
        {
            if ($this->isGranted("IS_AUTHENTICATED_FULLY")) {

                $currentWorkspace = $this->session->get('currentWorkspace');
                $domain = $this->getDoctrine()->getRepository(Domain::class)->find($currentWorkspace);
                $email = new Domain();

                $form = $this->createForm(addUsersFormType::Class, $email);

                $user = $this->getDoctrine()->getRepository(User::class)->findByEmail('admin@admin.admin');
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $domain->addDomainUser($user);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($domain);
                    $entityManager->flush();

                    return $this->redirectToRoute('app_addProject');
                }
                return $this->render('projects/addUserForm.html.twig', ['form' => $form->createView()]);
            } else
                return $this->redirectToRoute("app_welcome");

        }
}

