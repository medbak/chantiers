<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Profiler\Profiler;

Class BaseController extends AbstractController
{

    /**
     * méthode pour créer les entités et éviter les répitions du code
     * @param $entity
     */
    public function doctrineCreate($entity)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
    }
}