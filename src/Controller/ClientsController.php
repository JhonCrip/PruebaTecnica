<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Form\ClientsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientsController extends AbstractController
{
    /**
     * @Route("/", name="app_clients")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Clients::class)->findAllClients();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('clients/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/newClient", name="app_new_client")
     */
    public function newClient(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $client = new Clients();
        $form = $this->createForm(ClientsType::class, $client);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($client);
            $em->flush();
            $this->addFlash('success', 'Client Added Successfully');
            return $this->redirectToRoute('app_clients');
        }
        return $this->render('clients/newClient.html.twig', [
            'controller_name' => 'ClientsController',
            'formulario' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editClient/{id}", name="app_edit_client")
     */
    public function editClient($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository(Clients::class)->findOneById($id);
        $form = $this->createForm(ClientsType::class, $client);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($client);
            $em->flush();
            $this->addFlash('success', 'Client Edited Successfully');
            return $this->redirectToRoute('app_clients');
        }
        return $this->render('clients/editClient.html.twig', [
            'controller_name' => 'ClientsController',
            'formulario' => $form->createView(),
        ]);
    }

    /**
     * @Route("/removeClient/{id}", name="app_remove_client")
     */
    public function removeClient($id)
    {
        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository(Clients::class)->findOneById($id);
        $em->remove($client);
        $em->flush();
        $this->addFlash('success', 'Client Edited Successfully');
        return $this->redirectToRoute('app_clients');
    }
}
