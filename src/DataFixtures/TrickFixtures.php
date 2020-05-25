<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\TrickPicture;
use App\Entity\VideoPicture;

class TrickFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = Faker\Factory::create(); 

        $categories = [
            [
                'grab','Un grab consiste à attraper la planche avec la main pendant le saut.'
            ],
            [
                'rotation','On désigne par le mot rotation uniquement les rotations horizontales.'
            ],
            [
                'flip','Un flip est une rotation verticale.'
            ],
            [
                'slide','Un slide consiste à glisser sur une barre de slide.'
            ]
        ];

        $tricks = [
            [
                'mute', 0, 'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.',
                ['mute_1.jpg','mute_2.jpg','mute_3.jpg'],
                ['Opg5g4zsiGY','51sQRIK-TEI']
            ],
            [
                'sad', 0, 'Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.',
                ['sad_1.jpg','sad_2.jpg','sad_3.jpg'],
                ['KEdFwJ4SWq4','y2TDq06ptKc']
            ],
            [
                '180', 1, 'Demi-tour, soit 180 degrés d\'angle.',
                ['180_1.jpg','180_2.jpg','180_3.jpg'],
                ['ATMiAVTLsuc','JMS2PGAFMcE','VL9pPKsVB_4']
            ],
            [
                '1080', 1, 'Trois tours.',
                ['1080_1.jpg','1080_2.jpg','1080_3.jpg'],
                ['camHB0Rj4gA','_3C02T-4Uug']
            ],
            [
                'front flip', 2, 'Rotation en avant.',
                ['front_flip_1.jpg','front_flip_2.jpg'],                
                ['eGJ8keB1-JM','9_zC7CdvYu4','OJZQlS_gXoE']
            ],
            [
                'Back flip', 2, 'Rotation en arrière.',
                ['back_flip_1.jpg','back_flip_2.jpg'],                
                ['arzLq-47QFA','5bpzng08nzk']
            ],            
            [
                'indy', 0, 'Saisie de la carre frontside de la planche, entre les deux pieds avec la main arrière.',
                ['indy_1.jpg','indy_2.jpg','indy_3.jpg'],
                ['t0F1sKMUChA','6yA3XqjTh_w']
            ],
            [
                'nose grab', 0, 'Saisie de la partie avant de la planche, avec la main avant.',
                ['nose_grab_1.jpg','nose_grab_2.jpg'],
                ['M-W7Pmo-YMY','gZFWW4Vus-Q']
            ],
            [
                'japan', 0, 'Saisie de la partie avant de la planche, avec la main avant, du côté de la carre frontside.',
                ['japan_1.jpg','japan_2.jpg','japan_3.jpg'],
                ['jH76540wSqU','CzDjM7h_Fwo']
            ],
            [
                'seat belt', 0, 'Saisie du carre frontside de la planche à l\'arrière avec la main avant.',
                ['seat_belt_1.jpg','seat_belt_2.jpg'],
                ['4vGEOYNGi_c','WBd6W7at7fk']
            ]

        ];

        //Create all categories
        $nbrCategories = count($categories);
        for($i = 0; $i < $nbrCategories; $i++)
        {
            $categoryData = $categories[$i];
            $category = new Category();
            $category->setName($categoryData[0]);
            $category->setDescription($categoryData[1]);
            $manager->persist($category);

            //Create all tricks
            $nbrTricks = count($tricks);
            for($j = 0; $j < $nbrTricks; $j++)
            {
                $trickData = $tricks[$j];
                if($trickData[1] == $category->getName())
                {
                    $trick = new Trick();
                    $trick->setName($trickData[0])
                          ->setDescription($trickData[2])
                          ->setCreatedAt($faker->dateTimeBetween('-3 months', '-2 months'))
                          ->setCategory($category);
                    $manager->persist($trick);

                    //Create all pictures
                    // $nbrPictures = count($trickData[3]);
                    // for($k = 0; $k < $nbrPictures; $k++)
                    foreach($trickData[3] as $pictureData)
                    {
                        $picture = new TrickPicture();
                        $picture->setFileName($pictureData)
                                ->setTrick($trick);
                        $manager->persist($picture);
                    }

                    //Create all videos
                    foreach($trickData[4] as $videoData)
                    {
                        $video = new VideoPicture();
                        $video->setLink($videoData)
                                ->setTrick($trick);
                        $manager->persist($video);
                    }
                }
            }
        }

        $manager->flush();
    }
}
