<?php

namespace App\Controller\moderator;

use App\Entity\Permission;
use App\Entity\Utilisateur;
use App\Form\moderator\ModeratorPermissionType;
use App\Repository\PermissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/moderator/permission")
 * @IsGranted("ROLE_MODO")
 */
class ModeratorPermissionController extends AbstractController
{
    /**
     * @Route("/", name="moderator_permission_index", methods={"GET"})
     */
    public function index(PermissionRepository $permissionRepository): Response
    {
        return $this->render('moderator/permission/index.html.twig', [
            'permissions' => $permissionRepository->findAllDESC(),
        ]);
    }

    /**
     * @Route("/new", name="moderator_permission_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $permission = new Permission();
        $form = $this->createForm(ModeratorPermissionType::class, $permission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($permission);
            $entityManager->flush();

            return $this->redirectToRoute('moderator_permission_index');
        }

        return $this->render('moderator/permission/new.html.twig', [
            'permission' => $permission,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moderator_permission_show", methods={"GET"})
     */
    public function show(Permission $permission): Response
    {
        return $this->render('moderator/permission/show.html.twig', [
            'permission' => $permission,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="moderator_permission_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Permission $permission): Response
    {
        /** 
         * Si la permission a déjà été acceptée, on ne fait plus la différence avec le nombre de permissions de l'utilisateur. 
         * nb_total_permissions est donc égal à 0. :-) :-)
         * Si la permission n'a pas été acceptée, on calcule le nombre de jours que l'utilisateur demande.
         * Si l'état a été changé à true (si le congé a été accepté), on effectue la soustraction avec le nombre total de permissions restant.
         * */
        if ($permission->getEtat() == true) {
            $nb_total_permissions = 0;
            return $this->redirectToRoute("moderator_permission_index");
            dump($permission->getEtat());
            exit();
        } elseif ($permission->getEtat() === null || $permission->getEtat() == false) {
            $timePermission = date_diff($permission->getDateDebut(), $permission->getDateFin());

            $nbJoursPermissions = intval($timePermission->format('%d'));

            $nbHeuresPermissions = intval($timePermission->h)/8;

            $nb_total_permissions = $nbJoursPermissions + $nbHeuresPermissions;
        }

        $user = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['id' => $permission->getUtilisateur()]);
        //dump($user);
        $form = $this->createForm(ModeratorPermissionType::class, $permission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * Si l'état de la demande de congé passe de null (ou false) à true, 
             * l'intervalle de temps représentant le congé demandé sera soustrait au nombre de congés restants.
             */
            if ($permission->getEtat() === true) {
                $user->setNbPermissions($user->getNbPermissions() - $nb_total_permissions);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('moderator_permission_index');
        }

        return $this->render('moderator/permission/edit.html.twig', [
            'permission' => $permission,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moderator_permission_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Permission $permission): Response
    {
        if ($this->isCsrfTokenValid('delete'.$permission->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($permission);
            $entityManager->flush();
        }

        return $this->redirectToRoute('moderator_permission_index');
    }
}
