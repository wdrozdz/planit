<?php


namespace App\Controller;
use App\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class LevelController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("main/projects", name="app_displayProjects")
     */
public function displayProjects()
{
    if ($this->isGranted("IS_AUTHENTICATED_FULLY")) {
        $currentWorkspace = $this->session->get('currentWorkspace');

        $projects = $this->getDoctrine()
            ->getRepository(Project::class)
            ->getDomainsByUser($currentWorkspace);
        return $this->render('horizontal_navbar.html.twig', array('projects' => $projects));
    } else {
        return $this->redirectToRoute("main");
    }
}
}