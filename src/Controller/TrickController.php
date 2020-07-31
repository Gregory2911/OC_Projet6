<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Trick;
use App\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
        if(!$trick){
            $trick = new Trick();
        }
        

         //Create the admin user
        //  $user = new User();
        //  $username = 'essai';        
        //  $userPicture = 'avatar_1.png';
        //  $pictureFileName = "blabla";
        //  $user->setUsername($username)
        //       ->setPassword("blabla")
        //       ->setEmail('admin@snowtricks.fr')
        //       ->setIsConfirmed(1)
        //       ->setCreatedAt(new \DateTime())
        //       ->setPictureFilename($pictureFileName);
        // $manager->persist($user);
        
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){                                                       
            
            //Si modification
            if($trick->getId()){
                $trick->setModifiedAt(new \DateTime());
            }
            else{                
                $trick->setCreatedAt(new \DateTime()); 
                // $trick->setUser($user);
            }                        

            // submitted pictures handling
            // $submittedPictures = $trick->getTrickPictures();
            // foreach($submittedPictures as $submittedPicture){
                /** @var UploadedFile $file */
                // $file = $submittedPicture->getFile();

                // if file uploaded, because field not required
                
                    // $newFilename = $this->saveUploadedFile($file);
                    // $submittedPicture->setFilename("essai"); // store only the filename in database
                    // $manager->persist($submittedPicture);
                
            // }            

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
