<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $user->setCreatedAt(new \DateTime());

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(){
        return $this->render('security/login.html.twig');
    }

    /** 
     * @Route("/deconnexion", name="security_logout")
    */
    public function logout(){}

    /**
     * @Route("/forgot_password", name="security_forgot_password")
     */
    public function forgotPassword(Request $request, EntityManagerInterface $manager){

        $form = $this->createFormBuilder()
                     ->add('username',TextType::class)
                     ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $username = $form->getData()['username'];

            // $repo = $this->getDoctrine()->getRepository(User::class);

            // $user = $repo->findOneBy([
            //     'username' => $username
            // ]);

            $user = $manager->getRepository(User::class)->findOneBy([
                'username' => $username
            ]);

            if($user == false){
                var_dump($user);
                // die();
                $this->addFlash('warning','Pseudo inconnu !');
                return $this->render('security/forgot_password.html.twig',[
                    'form' => $form->createView()
                ]);
            }
            
        }
        else{
            return $this->render('security/forgot_password.html.twig',[
                'form' => $form->createView()
            ]);
        }
    }

}
