<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/utilisateur")
 * @IsGranted("ROLE_ADMIN")
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
     * @Route("/{id}/utilisateur_reset_password", name="utilisateur_reset_password")
     */
    public function reset_password(Utilisateur $utilisateur) {

    }

    private function redirectUnlessGranted(Utilisateur $utilisateur) {
        if ($this->getUser()->getId() !== $utilisateur->getId()) {
            $this->redirectToRoute('index');
        }
    }
}
