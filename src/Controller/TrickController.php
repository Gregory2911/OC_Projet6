<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('trick/home.html.twig');
    }
    
    /**
     * @Route("/tricks/{limit}/{offset}", name="index")
     */
    public function index($limit = 0, $offset = 0)
    {

        $repo = $this->getDoctrine()->getRepository(Trick::class);

        $tricks = $repo->findBy(
            [],
            array('createdAt' => 'desc'),
            $limit,
            $offset
        );  

        if ($limit == 10) {
            return $this->render('trick/index.html.twig', [
                'controller_name' => 'TrickController',
                'tricks' => $tricks
            ]);
        } else {
            return $this->render('trick/load_more.html.twig', [
                'controller_name' => 'TrickController',
                'tricks' => $tricks
            ]);
        }
    }    

    /**
     * @Route("/trick/{id}", name="trick_show")
     */
    public function show($id)
    {
        $repo = $this->getDoctrine()->getRepository(Trick::class);

        $trick = $repo->find($id);

        return $this->render('trick/show.html.twig', [
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/add_trick", name="add_trick")
     * @Route("/edit_trick/{id}", name="edit_trick")
     */
    public function addTrick(Trick $trick = null, Request $request, EntityManagerInterface $manager){
        // $trick = new Trick();
        if(!$trick){
            $trick = new Trick();
        }

        // $form = $this->createFormBuilder($trick)
        //              ->add('name')
        //              ->add('description')
        //              ->add('category')
        //              ->getForm();
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //Si modification
            if($trick->getId()){
                $trick->setCreatedAt(new \DateTime());
            }
            else{
                $trick->modifiedAt(new \DateTime());
            }
        
            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }

        return $this->render('trick/add_trick.html.twig', [
            'formTrick' => $form->createView(),
            'editMode' => $trick->getId() !== null
        ]);
    }
}
