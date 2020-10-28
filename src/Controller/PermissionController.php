<?php

namespace App\Controller;

use App\Entity\Permission;
use App\Form\PermissionType;
use App\Repository\PermissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use DateTime;
/**
 * @Route("/permission")
 * @IsGranted("ROLE_USER")
 */
class PermissionController extends AbstractController
{
    /**
     * @Route("/", name="permission_index", methods={"GET"})
     */
    public function index(PermissionRepository $permissionRepository): Response
    {
        return $this->render('permission/index.html.twig', [
            'permissions' => $permissionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="permission_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $nb_permissions = $this->getUser()->getNbPermissions();

        if ($nb_permissions <= 0) {
            $this->addFlash('danger', 'Vous n\'avez plus de permission; veuillez prendre un congé.');
            return $this->redirectToRoute('conge_new');
        }

        $permission = new Permission();
        $form = $this->createForm(PermissionType::class, $permission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Est-ce que le nombre de jours de permission demandé est trop élevé ?
            $timePermission = date_diff($permission->getDateDebut(), $permission->getDateFin());

            $nbJoursPermissions = intval($timePermission->format('%d'));
            $nbHeuresPermissions = intval($timePermission->h)/8;
            $nb_total_permissions = $nbJoursPermissions + $nbHeuresPermissions;

            if ($nb_permissions - $nb_total_permissions < 0) {
                $this->addFlash('danger', 'Le nombre de jours de permission demandé est trop élevé.');
                return $this->redirectToRoute('permission_new');
            }

            $permission->setCreatedAt(new DateTime());
            $permission->setUtilisateur($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($permission);
            $entityManager->flush();

            $this->addFlash('success', 'Demande enregistrée avec succès.');

            return $this->redirectToRoute('index');
        }

        return $this->render('permission/new.html.twig', [
            'permission' => $permission,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="permission_show", methods={"GET"})
     */
    public function show(Permission $permission): Response
    {
        return $this->render('permission/show.html.twig', [
            'permission' => $permission,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="permission_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Permission $permission): Response
    {
        $form = $this->createForm(PermissionType::class, $permission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('permission/edit.html.twig', [
            'permission' => $permission,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="permission_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Permission $permission): Response
    {
        if ($this->isCsrfTokenValid('delete'.$permission->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($permission);
            $entityManager->flush();
        }

        return $this->redirectToRoute('index');
    }
}
