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

        //recovery of the main photo if it exists
        $pictures = $trick->getTrickPictures();
        $mainPicture = null;
        foreach ($pictures as $value) {
            if ($value->getMainPicture() == true) {
                $mainPicture = $value;
            }
        }

        //recovery of the comments
        $repo = $this->getDoctrine()->getRepository(Comment::class);
        $comments =  $repo->findBy(
            ['trick' => $trick],
            array('createdAt' => 'desc'),
            5,
            0
        );

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'commentForm' => $form->createView(),
            'mainPicture' => $mainPicture,
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/load_more_comments/{trickId}/{offset}", name="load_more_comments")
     */
    public function loadMoreComments($trickId, $offset)
    {

        if (isset($trickId) && isset($offset)) {
            $repo = $this->getDoctrine()->getRepository(Trick::class);

            $trick = $repo->findOneBy(
                ['id' => $trickId]
            );

            $repo = $this->getDoctrine()->getRepository(Comment::class);
            $comments =  $repo->findBy(
                ['trick' => $trick],
                array('createdAt' => 'desc'),
                5,
                $offset
            );
            return $this->render('trick/load_more_comments.html.twig', [
                'comments' => $comments
            ]);
        }
    }

    /**
     * @Route("/add_trick", name="add_trick")
     * @Route("/edit_trick/{id}", name="edit_trick")
     */
    public function addTrick(Trick $trick = null, Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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

            //submitted pictures handling
            $submittedPictures = $trick->getTrickPictures();
            $filename = new FilenameCreator();
            $filesystem = new Filesystem();
            $bMainPicture = 0;
            foreach ($submittedPictures as $submittedPicture) {
                /** @var UploadedFile $file */
                $file = $submittedPicture->getFile();
                $newFilename = $filename->createUniqueFilename($file->getClientOriginalName());
                try {
                    $file->move($this->getParameter('upload_images_directory') . '/trick', $newFilename);
                } catch (FileException $e) {
                    throw $e;
                }
                if ($submittedPicture->getMainPicture() == 1) {
                    $bMainPicture = 1;
                }
                $submittedPicture->setFilename($newFilename); // store only the filename in database
                $manager->persist($submittedPicture);
            }
            //registration of a main picture if not defined
            if ($bMainPicture == 0 && $submittedPictures[1] !== null) {
                $submittedPictures[1]->setMainPicture(1);
                $manager->persist($submittedPicture);
            }

            //submitted videos handling
            $submittedVideos = $trick->getTrickVideos();
            foreach ($submittedVideos as $submittedVideo) {
                $manager->persist($submittedVideo);
            }

            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }

        return $this->render('trick/add_trick.html.twig', [
            'formTrick' => $form->createView(),
            'editMode' => $trick->getId() !== null,
            'trick' => $trick,
            'pictures' => $trick->getTrickPictures()
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Trick $trick
     * @return void
     * @Route("/suppression_trick/{id}", name="delete_trick")
     */
    public function deleteTrick(Trick $trick)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    }
}
