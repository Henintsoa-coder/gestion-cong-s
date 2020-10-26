<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ChangePassType;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/utilisateur")
 * @IsGranted("ROLE_USER")
 */
class UtilisateurController extends AbstractController
{
    /**
     * @Route("/", name="utilisateur_index", methods={"GET"})
     */
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        $utilisateur = $utilisateurRepository->findOneBy(['id' => $this->getUser()]);

        dump($utilisateur);

        return $this->render('utilisateur/index.html.twig', [
            'utilisateur' => $utilisateur
        ]);
    }

    /**
     * @Route("/{id}", name="utilisateur_show", methods={"GET"})
     */
    public function show(Utilisateur $utilisateur): Response
    {
        $this->redirectUnlessGranted($utilisateur);
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="utilisateur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Utilisateur $utilisateur): Response
    {
        $this->redirectUnlessGranted($utilisateur);
        if ($this->getUser()->getId() !== $utilisateur->getId()) {
            $this->redirectToRoute('index');
        }

        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('utilisateur_index');
        }

        return $this->render('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/change_pass/{id}", name="change_password", methods={"GET","POST"})
     */
    public function changePassword(Utilisateur $utilisateur, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
    	$em = $this->getDoctrine()->getManager();
        $user = $utilisateur;
        $form = $this->createForm(ChangePassType::class, $user);
        
        if($this->getUser()->getId() !== $user->getId()){
            $this->addFlash('danger', 'vous n\'avez le droit d\'accéder au profil de cet utilisateur.');
            return $this->redirectToRoute('utilisateur_index');
        }   

    	$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$passwordEncoder = $this->get('security.password_encoder');
            /*dump($request->request);
            $oldPassword = $request->request->get('change_pass')['oldPassword'];
            dump($oldPassword);

            die();*/

            // Si l'ancien mot de passe est bon
                $newEncodedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($newEncodedPassword);
                $em->persist($user);
                $em->flush();
                
                $this->addFlash('success', 'Mot de passe modifié avec succès.');
                return $this->redirectToRoute('utilisateur_index');
           
        }
        

    	return $this->render('utilisateur/changePassword.html.twig', array(
    		'form' => $form->createView(),
    	));
    }

    /**
     * Redirige l'utilisateur vers le formulaire de login si celui n'a pas encore validé son e-mail
     */
    private function redirectUnlessGranted(Utilisateur $utilisateur) {
        if ($this->getUser()->getId() !== $utilisateur->getId()) {
            $this->redirectToRoute('index');
        }
    }
}
