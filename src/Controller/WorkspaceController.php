<?php


namespace App\Controller;

use App\Form\DomainFormType;
use App\Entity\Domain;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class WorkspaceController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("main", name="app_addWorkspace")
     */
    public function new(Request $request): Response
    {
        if ($this->isGranted("IS_AUTHENTICATED_FULLY")) {

            $domain = new Domain();
            $form = $this->createForm(DomainFormType::Class, $domain);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user = $this->getUser()->getID();
                $domain->setUsers($user);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($domain);
                $entityManager->flush();
                return $this->redirect("main");
            }

            return $this->render('workspaces/workspace_form.html.twig', ['form' => $form->createView()]);
        } else {
            return $this->redirectToRoute("app_welcome");
        }
    }

    /**
     * @Route("main", name="app_main")
     */
    public function showAction()
    {
        if ($this->isGranted("IS_AUTHENTICATED_FULLY")) {
            $currentUser = $this->getUser()->getID();

            $workspaces = $this->getDoctrine()
                ->getRepository(Domain::class)
                ->getDomainsByUser($currentUser);
            return $this->render('workspaces/workspaces.html.twig', array('workspaces' => $workspaces));
        } else {
            return $this->redirectToRoute("main");
        }
    }

    /**
     * @Route("main/delete/{id}")
     */

    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $domain = $this->getDoctrine()->getRepository(Domain::class)->find($id);

        $entityManager->remove($domain);
        $entityManager->flush();
        return $this->redirectToRoute("app_addWorkspace");

    }

    /**
     * @Route("main/select/{currentWorkspace}/{currentWorkspaceName}")
     */
    public function select($currentWorkspace, $currentWorkspaceName)
    {


        $this->session->set('currentWorkspace', $currentWorkspace);
        $this->session->set('currentWorkspaceName', $currentWorkspaceName);
        return $this->redirectToRoute("app_displayProjects");
    }

}
