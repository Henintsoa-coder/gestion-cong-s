<?php

namespace App\Controller\admin;

use App\Entity\Utilisateur;
use App\Form\admin\AdminUtilisateurType;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("admin/utilisateur")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminUtilisateurController extends AbstractController
{
    /**
     * @Route("/", name="admin_utilisateur_index", methods={"GET"})
     */
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('admin/utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="admin_utilisateur_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        return $this->redirectToRoute('app_register');
    }

    /**
     * @Route("/{id}", name="admin_utilisateur_show", methods={"GET"})
     */
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('admin/utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_utilisateur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Utilisateur $utilisateur): Response
    {
        $form = $this->createForm(AdminUtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_utilisateur_index');
        }

        return $this->render('admin/utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_utilisateur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Utilisateur $utilisateur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_utilisateur_index');
    }

    /**
     * @Route("/{id}/admin_utilisateur_reset_password", name="utilisateur_reset_password")
     */
    public function reset_password(Utilisateur $utilisateur) {

    }

}
