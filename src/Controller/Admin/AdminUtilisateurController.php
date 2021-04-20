<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUtilisateurController extends AbstractController
{
    /**
     * @var UtilisateurRepository
     */
    private $userRepo;
    private $em;

    public function __construct(UtilisateurRepository $userRepo, EntityManagerInterface $em)
    {
        $this->userRepo = $userRepo;
        $this->em = $em;
    }

    /**
     * @Route("/admin", name="admin.utilisateur.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $utilisateurs = $this->userRepo->findAll();
        return $this->render('admin/utilisateur/index.html.twig', compact('utilisateurs'));
    }

    /**
     * @Route ("/admin/utilisateur/create", name="admin.utilisateur.create")
     */
    public function create(Request $request)
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($utilisateur);
            $this->em->flush();
            $this->addFlash('success', 'Crée avec succès');
            return $this->redirectToRoute('admin.utilisateur.index');
        }

        return $this->render('admin/utilisateur/create.html.twig',[
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/utilisateur/{id}", name="admin.utilisateur.edit", methods="GET|POST")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Utilisateur $utilisateur, Request $request)
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Modifié avec succès');
            return $this->redirectToRoute('admin.utilisateur.index');
        }

        return $this->render('admin/utilisateur/edit.html.twig',[
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/utilisateur/{id}", name="admin.utilisateur.delete", methods="DELETE")
     * @param Utilisateur $utilisateur
     */
    public function delete(Utilisateur $utilisateur, Request $request)
    {
        if($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->get('_token')))
        {
            $this->em->remove($utilisateur);
            $this->em->flush();
            $this->addFlash('success', 'Supprimé avec succès');
        }
       // return new Response('suppression');
        return $this->redirectToRoute('admin.utilisateur.index');
    }
}