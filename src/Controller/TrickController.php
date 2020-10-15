<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Service\FilenameCreator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
    public function show(Trick $trick, Request $request, EntityManagerInterface $manager)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {
            $user = $this->getUser();
            $comment->setCreatedAt(new \DateTime());
            $comment->setTrick($trick);
            $comment->setUser($user);
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToroute('trick_show', ['id' => $trick->getId()]);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'commentForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/add_trick", name="add_trick")
     * @Route("/edit_trick/{id}", name="edit_trick")
     */
    public function addTrick(Trick $trick = null, Request $request, EntityManagerInterface $manager)
    {
        if (!$trick) {
            $trick = new Trick();
        }

        $user = $this->getUser();

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $trick->setUser($user);

            //Si modification
            if ($trick->getId()) {
                $trick->setModifiedAt(new \DateTime());
            } else {
                $trick->setCreatedAt(new \DateTime());
                // $trick->setUser($user);
            }


            // die();

            //submitted pictures handling
            $submittedPictures = $trick->getTrickPictures();
            $newFilename = new FilenameCreator();
            $filesystem = new Filesystem();

            foreach ($submittedPictures as $submittedPicture) {
                /** @var UploadedFile $essai */
                $essai = $submittedPicture->getFileName();
                $essai = pathinfo($essai->getClientOriginalName(),PATHINFO_FILENAME);                
                $trickPictureFileName = $newFilename->createUniqueFilename($originalFilename);                

                $submittedPicture->move('public/uploads/images',$trickPictureFileName);
                // $newPath = 'public/uploads/images' . $trickPictureFileName;
                // $filesystem->copy($uploadFile->getClientOriginalName(), $newPath, true); //copy the trickPictureData to upload image directory
                // $submittedPicture->setFilename($file); // store only the filename in database
                $manager->persist($submittedPicture);
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
