<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Service\FilenameCreator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    private $encoder;
    private $container;

    public function __construct(UserPasswordEncoderInterface $encoder, ContainerInterface $container)
	{
        $this->encoder = $encoder;
        $this->container = $container;
	}

    public function load(ObjectManager $manager)
    {
        $commentsDataset = ['Figure vraiment sympa','Très dur à réaliser','Je me suis cassé un bras en la réalisant','Si vous avez des astuces pour la réaliser... je n\'y arrive pas...', 'casque obligatoire ;)', 'Pas évidente à réaliser cette figure', 'Je cherche un prof particulier qui pourrait m\'apprendre à rider', 'coucou', 'la neige est bonne cette année !', 'cool', 'figure très sympa pour débuter'];

    	$faker = \Faker\Factory::create('fr_FR');
        $filesystem = new Filesystem();
        $newFilename = new FilenameCreator();

    	// Create 15 fake users who commented every tricks
    	for($i = 1; $i <= 15; $i++){
    		$user = new User();
    		$username = $faker->userName();
    		$hash = $this->encoder->encodePassword($user, $username);

			$userCreationDate = $faker->dateTimeBetween('-2 months', '-1 months');
						
			$userPicture = 'avatar_'.mt_rand(1, 9).'.png';
            $pictureFileName = $newFilename->createUniqueFilename($userPicture);

			$pathData = $this->container->getParameter('images_directory') . '/avatarsPicturesData/' . $userPicture;
            $newPath = $this->container->getParameter('upload_images_directory') . '/avatar/' . $pictureFileName;
            $filesystem->copy($pathData, $newPath, true); //copy the avatarPictureData to upload image directory
	   		
    		$user->setUsername($username)
    			 ->setEmail($username.'@'.$faker->safeEmailDomain())
    			 ->setPassword($hash)
    			 ->setIsConfirmed(1)
    			 ->setCreatedAt($userCreationDate)
    			 ->setPictureFilename($pictureFileName);
    		$manager->persist($user);

			$tricks = $manager->getRepository(Trick::class)->findAll();

			foreach($tricks as $trick){
		    	// Create between 0 and 2 fake comment by user, so one by user in average
	        	for($j = 1; $j <= mt_rand(0, 2); $j++){
	        		$daysSinceUserExist = (new \DateTime())->diff($userCreationDate)->days;
	        		$commentContent = $commentsDataset[mt_rand(0, count($commentsDataset)-1)]; //random content
	        		$comment = new Comment();
	        		$comment->setContent($commentContent)
	        				->setCreatedAt($faker->dateTimeBetween('-'.$daysSinceUserExist.' days'))
	        				->setUser($user)
	        				->setTrick($trick);
	        		$manager->persist($comment);
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
        return 2;
    }
}
