<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
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
                ->from('agnan.gregory@orange.fr')
                ->to($user->getEmail())
                ->subject('Finalisation de l\'inscription au site snowtricks')
                ->text('Pour finaliser votre inscription au site communautaire Snowtricks, veuillez cliquer sur lien suivant:')
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
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
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

        if ($user === null) {
            //send a 404 error
            throw $this->createNotFoundException('Token invalide.');
        }

        $user->setActivationToken(null);
        $user->setIsConfirmed(1);
        $manager->persist($user);
        $manager->flush();

        $this->addFlash('success', 'Votre compte a été activé, vous pouvez maintenant vous connecter.');

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/mot_de_passe_oublié", name="security_forgot_password")
     */
    public function forgotPassword(Request $request, EntityManagerInterface $manager, MailerInterface $mailer)
    {

        $form = $this->createFormBuilder()
            ->add('email', TextType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $email = $form->getData()['email'];

            $user = $manager->getRepository(User::class)->findOneBy([
                'email' => $email
            ]);

            if ($user === null) {
                // var_dump($user);
                // die();
                $this->addFlash('warning', 'Pseudo inconnu !');
                return $this->render('security/forgot_password.html.twig', [
                    'form' => $form->createView()
                ]);
            } else {

                $user->setActivationToken(md5(uniqid()));

                $manager->persist($user);
                $manager->flush();

                $url = $this->generateUrl('security_reset_password', [
                    'token' => $user->getActivationToken()
                ]);

                $email = (new Email())
                    ->from('agnan.gregory@orange.fr')
                    ->to($user->getEmail())
                    ->subject('Réinitialisation du mot de passe de votre compte au site Snowtricks')
                    ->text('Pour réinitialiser votre mot de passe, merci de cliquer sur le lien suivant.')
                    ->html('<a href="' . $url . '">Réinitialiser<\a>');

                $mailer->send($email);

                $this->addFlash('success', 'Pour réinitialiser votre mot de passe, merci de cliquer sur le lien qui vient de vous être envoyé par email.');

                return $this->redirectToRoute('home');
            }
        } else {
            return $this->render('security/forgot_password.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    /**
     * @Route("/réinitialisation_mot_de_passe/{token}", name="security_reset_password")
     */
    public function resetPassword($token, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {

        //try to find user with the token
        $user = $manager->getRepository(User::class)->findOneBy([
            'activationToken' => $token
        ]);

        if ($user === null) {
            //send a 404 error
            throw $this->createNotFoundException('Token invalide, merci de refaire votre demande.');
        } else {

            $form = $this->createFormBuilder()
                ->add('email', EmailType::class)
                ->add('password', PasswordType::class)
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $formData = $form->getData();

                $emailSending = $user->getEmail();

                if ($emailSending !== $formData['email']) {
                    $this->addFlash('warning', 'e-mail utilisateur inconnu');
                } else {

                    $hash = $encoder->encodePassword($user, $formData['password']);
                    $user->setPassword($hash);

                    $user->setActivationToken(null);

                    $manager->persist($user);

                    $manager->flush();

                    $this->addFlash('success', 'Votre mot de passe a bien été modifié');

                    return $this->redirectToRoute('home');
                }
            }

            return $this->render('security/reset_password.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }
}
