<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, MailerInterface $mailer)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $user->setCreatedAt(new \DateTime());

            $user->setActivationToken(md5(uniqid()));

            $manager->persist($user);
            $manager->flush();

            $url = $this->generateUrl('security_activation_registration', [
                'token' => $user->getActivationToken()
            ]);

            $email = (new Email())
                ->from('no-reply@snowtricks.fr')
                ->to($user->getEmail())
                ->subject('Finalisation de l\'inscription au site snowtricks')
                ->text('Pour finaliser votre inscription au site communautaire Snowtriks, veuillez cliquer sur lien suivant:')
                ->html('<a href="' . $url . '">Finaliser l\'inscription<\a>');

            $mailer->send($email);

            $this->addFlash('success', 'Pour finaliser votre inscription, merci de cliquer sur le lien qui vient de vous être envoyé par email.');

            return $this->redirectToRoute('home');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig');
    }

    /** 
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/activation/{token}", name="security_activation_registration")
     */
    public function activation($token, EntityManagerInterface $manager)
    {

        //try to find user with the token
        $user = $manager->getRepository(User::class)->findOneBy([
            'activationToken' => $token
        ]);

        if (!$user) {
            //send a 404 error
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        $user->setActivationToken(null);
        $manager->persist($user);
        $manager->flush();

        $this->addFlash('success', 'Votre compte a été activé, vous pouvez maintenant vous connecter.');

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/forgot_password", name="security_forgot_password")
     */
    public function forgotPassword(Request $request, EntityManagerInterface $manager)
    {

        $form = $this->createFormBuilder()
            ->add('username', TextType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $username = $form->getData()['username'];

            // $repo = $this->getDoctrine()->getRepository(User::class);

            // $user = $repo->findOneBy([
            //     'username' => $username
            // ]);

            $user = $manager->getRepository(User::class)->findOneBy([
                'username' => $username
            ]);

            if ($user == false) {
                var_dump($user);
                // die();
                $this->addFlash('warning', 'Pseudo inconnu !');
                return $this->render('security/forgot_password.html.twig', [
                    'form' => $form->createView()
                ]);
            }
        } else {
            return $this->render('security/forgot_password.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }
}
