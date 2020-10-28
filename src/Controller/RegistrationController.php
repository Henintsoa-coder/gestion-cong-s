<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

use Swift_SmtpTransport;
use Swift_Message;

class RegistrationController extends AbstractController
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
                ->setUserName('entreprise.henintsoa1987@gmail.com', 'ECR')
				->setPassword('leonidas1987')
				->setStreamOptions(
                    array('ssl' => array('allow_self_signed' => true, 'verify_peer' => false))
                );
        
        //Assignation de la variable $this->mailer 
        if ($this->mailer === null) {
            $this->mailer = new \Swift_Mailer($transport);
        }

        //Assignation de $manager
        $this->manager = $manager;
    }

    /**
     * @Route("/register", name="app_register")
     * Seuls les administrateurs peuvent y accéder
     * 
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, TokenGeneratorInterface $tokenGenerator): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password      
            $hash =  $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            
            //On génère le token d'activation
            $token = $tokenGenerator->generateToken();

            $user->setActivationToken($token);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            //On crée le message
            $message = (new Swift_Message('Activation de votre compte'))
            ->setFrom('alexraven995@gmail.com')
            ->setTo($user->getEmail()) //A remplacer par ->setTo($user->getEmail()).
            ->setBody(
                $this->renderView('emails/activation.html.twig', [
                    'token' => $user->getActivationToken()
                    ]
                ), 
                'text/html'
            );
        
            //On envoie le message
            $this->mailer->send($message);
            
            $this->addFlash('success', 'Salarié enregistré avec succès');
            
            return $this->redirectToRoute('admin_utilisateur_index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, UtilisateurRepository  $userRepository)
    {
        //On vérifie si un utilisateur a ce token
        $user = $userRepository->findOneBy(['activation_token' => $token]);

        //Si aucun utilisateur n'existe avec ce token
        if (!$user) {
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas.');
        }

        //On supprime le token
        $user->setActivationToken(null);
        $this->manager->persist($user);
        $this->manager->flush();
        
        //On envoie un message flash
        $this->addFlash('success', 'Vous avez bien activé votre compte');

        //On retourne à l'accueil
        return $this->redirectToRoute('app_login');
    }
}
