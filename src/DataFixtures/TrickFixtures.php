<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TrickFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categories = [
            'grab',
            'rotation',
            'flip',
            'slide'            
        ];

        $tricks = [
            [
                'mute', 0, 'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.',

            ],
            [
                'sad', 0, 'Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.',

            ],
            [
                '180', 1, 'Demi-tour, soit 180 degrés d\'angle.',

            ],
            [
                '1080', 1, 'Trois tours.',

            ],
            [
                'front flip', 2, 'Rotation en avant.',

            ],
            [
                'Back flip', 2, 'Rotation en arrière.',

            ],
            [
                'nose slide', 3, 'Glisser sur une barre avec l\'avant de la planche sur la barre.',

            ],
            [
                'tail slide', 3, 'Glisser sur une barre avec l\'arrière de la planche sur la barre.',

            ],
            [
                'indy', 0, 'Saisie de la carre frontside de la planche, entre les deux pieds avec la main arrière.',

            ],
            [
                'nose grab', 0, 'Saisie de la partie avant de la planche, avec la main avant.',
                  
            ],
            [
                'japan', 0, 'Saisie de la partie avant de la planche, avec la main avant, du côté de la carre frontside.',
                  
            ],
            [
                'seat belt', 0, 'Saisie du carre frontside de la planche à l\'arrière avec la main avant.',
                  
            ]

        ];

        $manager->flush();
    }
}
