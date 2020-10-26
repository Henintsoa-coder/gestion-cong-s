<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ResetPassType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    
    public function __construct(EntityManagerInterface $manager)
    {
        //Définition de la variable $transport.
        $transport = (new  Swift_SmtpTransport('smtp.gmail.com', 587, 'tls'))
                ->setUserName('entreprise.henintsoa1987@gmail.com')
				->setPassword('leonidas1987')
				->setStreamOptions(
                    array('ssl' => array('allow_self_signed' => true, 'verify_peer' => false))
                );
        
        //Assignation de la variable $this->mailer 
        if ($this->mailer === null) {
            $this->mailer = new Swift_Mailer($transport);
        }

        //Assignation de $manager
        $this->manager = $manager;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {   
        dump($this->getUser());

        if ($this->getUser()) {
             return $this->redirectToRoute('index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        //throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/oubli-pass", name="app_forgotten_password")
     */
    public function forgottenPass(Request $request, UtilisateurRepository $utilisateurRepository, TokenGeneratorInterface $tokenGenerator){
        //On crée le formualaire
        $form = $this->createForm(ResetPassType::class);

        //On fait le traitement
        $form->handleRequest($request);

        //Si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid()) {
            //On récupère les données
            $donnees = $form->getData(); //Quelle est la différence entre getData et handleRequest ?

            //On cherche si un utilisateur a cet e-mail
            $user = $utilisateurRepository->findOneBy(['email' => $donnees['email']]);

            //Si l'utilisateur n'existe pas
            if (!$user) {
                //On envoie un message flash
                $this->addFlash('danger', 'Cette adresse n\existe pas');
                return $this->redirectToRoute('app_login'); 
            }

            //On génère un token
            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $this->manager->persist($user);
                $this->manager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Une erreur est survenue' . $e->getMessage());
                return $this->redirectToRoute('app_login');
            }

            //On génère l'URL de réinitialisation de mot de passe *
            $url = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            //On crée le message
            $message = (new Swift_Message('Mot de passe oublié'))
                //On attribue l'expéditeur
                ->setFrom('entreprise.henintsoa1987@gmail.com', 'ECR')
                //On attribue le destinataire
                ->setTo($user->getEmail())//à remplacer par ->setTo($user->getEmail()) plus tard
                //On crée le contenu
                ->setBody(
                        "
                            <h1>Réinitialisation du mot de passe.</h1>
                            <p>Bonjour,</p>
                            <p>
                                Une demande de réinitialisation de mot de passe a été effectuée pour le site Symblog.mg. 
                                Veuillez cliquer sur le lien suivant :
                        "   
                            . $url .
                        "</p>"
                    , 
                    'text/html'
                );
            
                //On envoie l'e-mail
                $this->mailer->send($message);

                //On crée le message flash
                $this->addFlash('success', 'Un e-mail de réinitialisation de mot de passe vous a été envoyé.' );

                return $this->redirectToRoute('app_login');
        }

        //On renvoie vers le formulaire de demande d'e-mail
        return $this->render('security/forgotten_password.html.twig', ['emailForm' => $form->createView()]);
    }

     /**
     * @Route("/reset-pass/{token}", name="app_reset_password")
     */
    public function resetPassword($token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        //On cherche l'utilisateur avec le token fourni
        $user = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['reset_token' => $token]);

        if (!$user) 
        {
            $this->addFlash('danger', 'Token inconnu.');
            return $this->redirectToRoute('app_login');
        }

        //Si le formulaire est envoyé en méthode POST
        if ($request->isMethod('POST')) {
            //On supprime le token
            $user->setResetToken(null);

            //On chiffre le mot de passe
            $user->setPassword($passwordEncoder->encodePassword($user, $request->get('password'))); 
            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success', 'Mot de passe modifié avec succès.');

            return $this->redirectToRoute('app_login');
        } else {
            return $this->render('security/resetPassword.html.twig', ['token' => $token]);
        }
    }
}
