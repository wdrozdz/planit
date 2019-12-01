<?php


namespace App\Controller;
use App\Form\DomainFormType;
use App\Entity\Domain;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MainController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("addWorkspace", name="app_addWorkspace")
     */
    public function new(Request $request): Response
    {
        if ($this->isGranted("IS_AUTHENTICATED_FULLY")) {


            //adds domain
            $domain = new Domain();
            $form = $this->createForm(DomainFormType::Class, $domain);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $User = $this->getUser()->getID();
                $domain->setUsers($User);
                $domain->setUrl(rand(100000, 999999));

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($domain);
                $entityManager->flush();
                return $this->redirect("main");
            }

            return $this->render('main.html.twig', ['form' => $form->createView()]);
        } else {
            return $this->redirectToRoute("app_login");
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
            return $this->render('workspaces.html.twig', array('workspaces' => $workspaces));
        } else {
            return $this->redirectToRoute("app_login");
        }
    }

    /**
     * @Route("main/delete/{id}")
     */
    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $workspaces = $this->getDoctrine()->getRepository(Domain::class)->find($id);

        $entityManager->remove($workspaces);
        $entityManager->flush();
        return $this->redirectToRoute("app_main");

    }

    /**
     * @Route("main/select/{id}")
     */
    public function select($id)
    {
        $this->session->set('currentWorkspace', $id);
        return $this->redirectToRoute("app_main");
    }

}
