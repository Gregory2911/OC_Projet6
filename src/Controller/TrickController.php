<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Service\SlugGenerator;
use App\Service\FilenameCreator;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @Route("/trick/{slug}", name="trick_show")
     */
    public function show(string $slug, TrickRepository $repo, Request $request, EntityManagerInterface $manager)
    {

        $trick = $repo->findOneBy(['slug' => $slug]); // getting the trick by slug

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

            return $this->redirectToroute('trick_show', ['slug' => $trick->getSlug()]);
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
            10,
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
                10,
                $offset
            );
            return $this->render('trick/load_more_comments.html.twig', [
                'comments' => $comments
            ]);
        }
    }

    /**
     * @Route("/add_trick", name="add_trick")
     */
    public function addTrick(Trick $trick = null, Request $request, EntityManagerInterface $manager, SlugGenerator $slugGenerator)
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $trick = new Trick();

        $user = $this->getUser();

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $trick->setUser($user);

            $trick->setSlug($slugGenerator->convert($trick->getName()));

            $trick->setCreatedAt(new \DateTime());

            //submitted pictures handling
            $submittedPictures = $trick->getTrickPictures();
            $filename = new FilenameCreator();
            $filesystem = new Filesystem();
            $bMainPicture = 0;
            foreach ($submittedPictures as $submittedPicture) {
                if ($submittedPicture->getId() == null) {
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

            $this->addFlash('success', 'Votre trick a bien été ajouté !');
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/add_trick.html.twig', [
            'formTrick' => $form->createView(),
            'editMode' => false,
            'trick' => $trick,
            'pictures' => $trick->getTrickPictures()
        ]);
    }

    /**
     * @Route("/edit_trick/{id}", name="edit_trick")
     */
    public function editTrick(Trick $trick, Request $request, EntityManagerInterface $manager, SlugGenerator $slugGenerator)
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // $user = $this->getUser();

        // Store initial pictures of the trick to compare
        $originalPictures = new ArrayCollection();
        foreach ($trick->getTrickPictures() as $picture) {
            $originalPictures->add($picture);
        }

        // Store initial videos of the trick to compare
        $originalVideos = new ArrayCollection();
        foreach ($trick->getTrickVideos() as $video) {
            $originalVideos->add($video);
        }

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {

            $trick->setModifiedAt(new \DateTime())
                ->setSlug($slugGenerator->convert($trick->getName()));

            //submitted pictures handling
            $submittedPictures = $trick->getTrickPictures();
            $filename = new FilenameCreator();
            $filesystem = new Filesystem();
            $bMainPicture = 0;
            foreach ($submittedPictures as $submittedPicture) {
                if ($submittedPicture->getId() == null) {
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
            }

            // check for removed Pictures
            foreach ($originalPictures as $picture) {
                // if the picture is missing in submission
                if (false === $trick->getTrickPictures()->contains($picture)) {
                    $picture->setTrick(null); // remove the relationship
                    $manager->persist($picture);
                    $manager->remove($picture); // delete the picture from database
                    $this->deleteUploadedFile($picture->getFilename()); // delete thepicture from files
                } elseif ($picture->getMainPicture()) {
                    $bMainPicture = 1;
                }
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

            // check for removed Videos
            foreach ($originalVideos as $video) {
                if (false === $trick->getTrickVideos()->contains($video)) {
                    $video->setTrick(null); // remove the relationship 
                    $manager->persist($video);
                    $manager->remove($video); // delete the video
                }
            }

            $manager->persist($trick);
            // dump($trick);
            // die();
            $manager->flush();

            $this->addFlash('success', 'Votre trick a bien été modifié !');
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/add_trick.html.twig', [
            'formTrick' => $form->createView(),
            'editMode' => true,
            'trick' => $trick,
            'pictures' => $trick->getTrickPictures()
        ]);
    }

    /**
     * @Route("/suppression_trick/{id}", name="delete_trick")
     */
    public function deleteTrick(Trick $trick, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        //remove Pictures
        foreach ($trick->getTrickPictures() as $picture) {
            $trick->removeTrickPicture($picture); //delete the picture from the entity
            $manager->remove($picture); //delete the picture from the database
            $this->deleteUploadedFile($picture->getFileName()); //delete the uploaded picture
        }

        //remove videos
        foreach ($trick->getTrickVideos() as $video) {
            $trick->removeTrickVideo($video);
            $manager->remove($video);
        }

        //remove comments
        foreach ($trick->getComments() as $comment) {
            $trick->removeComment($comment);
            $manager->remove($comment);
        }

        $manager->remove($trick);
        $manager->flush();

        $this->addFlash('success', 'Le trick a bien été supprimé');

        return $this->redirectToRoute('home');
    }

    /**
     * Delete the uploaded file
     */
    public function deleteUploadedFile(string $filename)
    {
        $filesystem = new Filesystem();
        $path = $this->getParameter('upload_images_directory') . '/trick/' . $filename;
        $filesystem->remove($path);
    }
}
