<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Trick;

class TrickController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(Trick::class);

        $tricks = $repo->findAll();

        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/trick/{id}", name="trick_show")
     */
    public function show($id)
    {
        $repo = $this->getDoctrine()->getRepository(Trick::class);

        $trick = $repo->find($id);

        return $this->render('trick/show.html.twig', [
            'trick' -> $trick
        ]);
    }
}
