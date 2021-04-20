<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

Class UtilisateurController extends BaseController
{

    /**
     * @var UtilisateurRepository
     */
    private $userRepo;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(UtilisateurRepository $userRepo, EntityManagerInterface $em)
    {
        $this->userRepo = $userRepo;
        $this->em = $em;
    }

    /**
     * @Route("/utilisateur", name="utilisateur.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $utilisateurs = $this->userRepo->findAll();

        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurs,
            'current_menu' => 'utilisateurs',
        ]);
    }

    /**
     * @Route("/utilisateur/{slug}-{id}", name="utilisateur.show", requirements={"slug" : "[a-z0-9\-]*"})
     * @return Response
     */
    public function show(Utilisateur $user, string $slug):Response
    {
        if($user->getSlug() !== $slug)
        {
            return $this->redirectToRoute('utilisateur.show', [
                'id' => $user->getId(),
                'slug' => $user->getSlug()
            ], 301);
        }

        return $this->render('utilisateur/show.html.twig', [
            'current_menu' => 'utilisateurs',
            'utilisateur' => $user
        ]);
    }

    /**
     * @Route ("/utilisateur/create", name="utilisateur.create")
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
            return $this->redirectToRoute('utilisateur.index');
        }

        return $this->render('utilisateur/create.html.twig',[
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/utilisateur/{id}", name="utilisateur.edit", methods="GET|POST")
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
            return $this->redirectToRoute('utilisateur.index');
        }

        return $this->render('utilisateur/edit.html.twig',[
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/utilisateur/{id}", name="utilisateur.delete", methods="DELETE")
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
        return $this->redirectToRoute('utilisateur.index');
    }


}
