<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Category;
use App\Entity\TrickVideo;
use App\Entity\TrickPicture;
use App\Service\SlugGenerator;
use App\Service\FilenameCreator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TrickFixtures extends Fixture implements OrderedFixtureInterface
{
    private $encoder;
    private $container;
    private $slugGenerator;

    public function __construct(UserPasswordEncoderInterface $encoder, ContainerInterface $container, SlugGenerator $slugGenerator)
    {
        $this->encoder = $encoder;
        $this->container = $container;
        $this->slugGenerator = $slugGenerator;
    }

    public function load(ObjectManager $manager)
    {
        // $encoder = new UserPasswordEncoderInterface();
        $faker = \Faker\Factory::create('fr_FR');
        $filesystem = new Filesystem();
        $newFilename = new FilenameCreator();

        $categories = [
            [
                'grab', 'Un grab consiste à attraper la planche avec la main pendant le saut.'
            ],
            [
                'rotation', 'On désigne par le mot rotation uniquement les rotations horizontales.'
            ],
            [
                'flip', 'Un flip est une rotation verticale.'
            ],
            [
                'slide', 'Un slide consiste à glisser sur une barre de slide.'
            ]
        ];

        $tricks = [
            [
                'mute', 0, 'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.',
                ['mute_1.jpg', 'mute_2.jpg', 'mute_3.jpg'],
                ['Opg5g4zsiGY', '51sQRIK-TEI']
            ],
            [
                'sad', 0, 'Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.',
                ['sad_1.jpg', 'sad_2.jpg', 'sad_3.jpg'],
                ['KEdFwJ4SWq4', 'y2TDq06ptKc']
            ],
            [
                '180', 1, 'Demi-tour, soit 180 degrés d\'angle.',
                ['180_1.jpg', '180_2.jpg', '180_3.jpg'],
                ['ATMiAVTLsuc', 'JMS2PGAFMcE', 'VL9pPKsVB_4']
            ],
            [
                '1080', 1, 'Trois tours.',
                ['1080_1.jpg', '1080_2.jpg', '1080_3.jpg'],
                ['camHB0Rj4gA', '_3C02T-4Uug']
            ],
            [
                'front flip', 2, 'Rotation en avant.',
                ['front_flip_1.jpg', 'front_flip_2.jpg'],
                ['eGJ8keB1-JM', '9_zC7CdvYu4', 'OJZQlS_gXoE']
            ],
            [
                'Back flip', 2, 'Rotation en arrière.',
                ['back_flip_1.jpg', 'back_flip_2.jpg'],
                ['arzLq-47QFA', '5bpzng08nzk']
            ],
            [
                'indy', 0, 'Saisie de la carre frontside de la planche, entre les deux pieds avec la main arrière.',
                ['indy_1.jpg', 'indy_2.jpg', 'indy_3.jpg'],
                ['t0F1sKMUChA', '6yA3XqjTh_w']
            ],
            [
                'nose grab', 0, 'Saisie de la partie avant de la planche, avec la main avant.',
                ['nose_grab_1.jpg', 'nose_grab_2.jpg'],
                ['M-W7Pmo-YMY', 'gZFWW4Vus-Q']
            ],
            [
                'japan', 0, 'Saisie de la partie avant de la planche, avec la main avant, du côté de la carre frontside.',
                ['japan_1.jpg', 'japan_2.jpg', 'japan_3.jpg'],
                ['jH76540wSqU', 'CzDjM7h_Fwo']
            ],
            [
                'seat belt', 0, 'Saisie du carre frontside de la planche à l\'arrière avec la main avant.',
                ['seat_belt_1.jpg', 'seat_belt_2.jpg'],
                ['4vGEOYNGi_c', 'WBd6W7at7fk']
            ]

        ];

        $dateCreation = $faker->dateTimeBetween('-3 months', '-2 months');

        //Create the admin user
        $user = new User();
        $username = 'admin';
        $hash = $this->encoder->encodePassword($user, $username);
        $userPicture = 'avatar_1.png';

        $pictureFileName = $newFilename->createUniqueFilename($userPicture);

        $pathData = $this->container->getParameter('images_directory') . '/avatarsPicturesData/' . $userPicture;
        $newPath = $this->container->getParameter('upload_images_directory') . '/avatar/' . $pictureFileName;
        $filesystem->copy($pathData, $newPath, true); //copy the avatarPictureData to upload image directory

        $user->setUsername($username)
            ->setPassword($hash)
            ->setEmail('admin@snowtricks.fr')
            ->setIsConfirmed(1)
            ->setCreatedAt($dateCreation)
            ->setPictureFilename($pictureFileName);

        $manager->persist($user);

        //Create all categories
        $nbrCategories = count($categories);
        for ($i = 0; $i < $nbrCategories; $i++) {
            $categoryData = $categories[$i];
            $category = new Category();
            $category->setName($categoryData[0]);
            $category->setDescription($categoryData[1]);
            $manager->persist($category);

            //Create all tricks
            $nbrTricks = count($tricks);
            for ($j = 0; $j < $nbrTricks; $j++) {
                $trickData = $tricks[$j];
                if ($trickData[1] == $i) {
                    $trick = new Trick();
                    $trick->setName($trickData[0])
                        ->setDescription($trickData[2])
                        ->setCreatedAt($dateCreation)
                        ->setUser($user)
                        ->setCategory($category)
                        ->setSlug($this->slugGenerator->convert($trickData[0]));
                    $manager->persist($trick);

                    //Create all pictures
                    $bOkMainPicture = 0;
                    foreach ($trickData[3] as $pictureData) {
                        $picture = new TrickPicture();
                        $trickPictureFileName = $newFilename->createUniqueFilename($pictureData);
                        $pathData = $this->container->getParameter('images_directory') . '/tricksPicturesData/' . $pictureData;
                        $newPath = $this->container->getParameter('upload_images_directory') . '/trick/' . $trickPictureFileName;
                        $filesystem->copy($pathData, $newPath, true); //copy the trickPictureData to upload image directory
                        $picture->setFileName($trickPictureFileName)
                            ->setTrick($trick);
                        if ($bOkMainPicture == 0) {
                            $picture->setMainPicture(true);
                            $bOkMainPicture = 1;
                        }
                        $manager->persist($picture);
                    }

                    //Create all videos
                    foreach ($trickData[4] as $videoData) {
                        $video = new TrickVideo();
                        $video->setLink('https://www.youtube.com/embed/' . $videoData)
                            ->setTrick($trick);
                        $manager->persist($video);
                    }
                }
            }
        }

        $manager->flush();
    }

    /*
        Define the order in which fixures will be loaded
    */
    public function getOrder()
    {
        return 1;
    }
}
