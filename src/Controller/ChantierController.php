<?php

namespace App\Controller;

use App\Entity\Chantier;
use App\Form\ChantierType;
use App\Repository\ChantierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

Class ChantierController extends BaseController
{

    /**
     * @var ChantierRepository
     */
    private $chantierRepo;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(ChantierRepository $chantierRepo, EntityManagerInterface $em)
    {
        $this->chantierRepo = $chantierRepo;
        $this->em = $em;
    }

    /**
     * @Route("/chantier", name="chantier.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $chantiers = $this->chantierRepo->findAll();

        return $this->render('chantier/index.html.twig', [
            'chantiers' => $chantiers,
            'current_menu' => 'chantiers',
        ]);
    }

    /**
     * @Route("/chantier/{slug}-{id}", name="chantier.show", requirements={"slug" : "[a-z0-9\-]*"})
     * @return Response
     */
    public function show(Chantier $chantier, string $slug):Response
    {
        if($chantier->getSlug() !== $slug)
        {
            return $this->redirectToRoute('chantier.show', [
                'id' => $chantier->getId(),
                'slug' => $chantier->getSlug()
            ], 301);
        }

        $qb = $this->em->createQueryBuilder();

        // le nombre de personne différentes
        //ayant été pointés sur le chantier
        $qb->select($qb->expr()->countDistinct('u.utilisateur'))
            ->from('App\Entity\Pointage', 'u')
            ->where('u.chantier = ?1')
            ->setParameter(1, $chantier->getId());
        $query = $qb->getQuery();
        $usersCount = $query->getSingleScalarResult();

        //nombre d’heures cumulés pointés sur ce chantier
        $query = $this->em->createQuery("SELECT SUM(p.duree) FROM App\Entity\Pointage p WHERE p.chantier = ?1");
        $query->setParameter(1, $chantier->getId());
        $sommeDuree = $query->getSingleScalarResult();

        return $this->render('chantier/show.html.twig', [
            'current_menu' => 'chantiers',
            'chantier' => $chantier,
            'nombre_personne' => $usersCount,
            'duree' => ($sommeDuree == null ? 0: $sommeDuree)
        ]);
    }

    /**
     * @Route ("/chantier/create", name="chantier.create")
     */
    public function create(Request $request)
    {
        $chantier = new Chantier();
        $form = $this->createForm(ChantierType::class, $chantier);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($chantier);
            $this->em->flush();
            $this->addFlash('success', 'Crée avec succès');
            return $this->redirectToRoute('chantier.index');
        }

        return $this->render('chantier/create.html.twig',[
            'chantier' => $chantier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/chantier/{id}", name="chantier.edit", methods="GET|POST")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Chantier $chantier, Request $request)
    {
        $form = $this->createForm(chantierType::class, $chantier);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Modifié avec succès');
            return $this->redirectToRoute('chantier.index');
        }

        return $this->render('chantier/edit.html.twig',[
            'chantier' => $chantier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/chantier/{id}", name="chantier.delete", methods="DELETE")
     * @param Chantier $chantier
     */
    public function delete(Chantier $chantier, Request $request)
    {
        if($this->isCsrfTokenValid('delete'.$chantier->getId(), $request->get('_token')))
        {
            $this->em->remove($chantier);
            $this->em->flush();
            $this->addFlash('success', 'Supprimé avec succès');
        }
        // return new Response('suppression');
        return $this->redirectToRoute('chantier.index');
    }


}
