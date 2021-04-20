<?php

namespace App\Controller;

use App\Entity\Pointage;
use App\Form\PointageType;
use App\Repository\ChantierRepository;
use App\Repository\PointageRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

Class PointageController extends BaseController
{

    /**
     * @var PointageRepository
     */
    private $pointageRepo;

    /**
     * @var UtilisateurRepository
     */
    private $userRepo;

    /**
     * @var ChantierRepository
     */
    private $chantierRepo;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(PointageRepository $pointageRepo, UtilisateurRepository $userRepo, ChantierRepository $chantierRepo , EntityManagerInterface $em)
    {
        $this->pointageRepo = $pointageRepo;
        $this->userRepo = $userRepo;
        $this->chantierRepo = $chantierRepo;
        $this->em = $em;
    }

    /**
     * @Route("/pointage", name="pointage.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $pointages = $this->pointageRepo->findAll();

        return $this->render('pointage/index.html.twig', [
            'pointages' => $pointages,
            'current_menu' => 'pointages',
        ]);
    }

    /**
     * @Route("/pointage/{slug}-{id}", name="pointage.show", requirements={"slug" : "[a-z0-9\-]*"})
     * @return Response
     */
    public function show(Pointage $pointage, string $slug):Response
    {
        if($pointage->getSlug() !== $slug)
        {
            return $this->redirectToRoute('pointage.show', [
                'id' => $pointage->getId(),
                'slug' => $pointage->getSlug()
            ], 301);
        }

        return $this->render('pointage/show.html.twig', [
            'current_menu' => 'pointages',
            'pointage' => $pointage
        ]);
    }

    /**
     * @Route ("/pointage/create", name="pointage.create")
     */
    public function create(Request $request)
    {
        $pointage = new Pointage();
        $form = $this->createForm(PointageType::class, $pointage);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($pointage);
            $this->em->flush();
            $this->addFlash('success', 'Crée avec succès');
            return $this->redirectToRoute('pointage.index');
        }

        return $this->render('pointage/create.html.twig',[
            'pointage' => $pointage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/pointage/{id}", name="pointage.edit", methods="GET|POST")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Pointage $pointage, Request $request)
    {
        $form = $this->createForm(PointageType::class, $pointage);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Modifié avec succès');
            return $this->redirectToRoute('pointage.index');
        }

        return $this->render('pointage/edit.html.twig',[
            'pointage' => $pointage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/pointage/{id}", name="pointage.delete", methods="DELETE")
     * @param Pointage $pointage
     */
    public function delete(Pointage $pointage, Request $request)
    {
        if($this->isCsrfTokenValid('delete'.$pointage->getId(), $request->get('_token')))
        {
            $this->em->remove($pointage);
            $this->em->flush();
            $this->addFlash('success', 'Supprimé avec succès');
        }
        // return new Response('suppression');
        return $this->redirectToRoute('pointage.index');
    }


}
