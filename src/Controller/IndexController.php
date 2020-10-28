<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\AbsenceRepository;
use App\Repository\CongeRepository;
use App\Repository\PermissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @IsGranted("ROLE_USER")
     */
    public function index(CongeRepository $congeRepository, PermissionRepository $permissionRepository, AbsenceRepository $absenceRepository)
    {
        
        
        $user = $this->getUser()?$this->getUser()->getUserName():'';
        $nb_conges = $this->getUser()?$this->getUser()->getNbConges():'';
        $nb_permissions = $this->getUser()?$this->getUser()->getNbPermissions():'';
        $congesUser = $congeRepository->findByUtilisateurId($this->getUserId());
        $permissionsUser = $permissionRepository->findByUtilisateurId($this->getUserId());
        $absencesUser = $absenceRepository->findByUtilisateurId($this->getUserId());
        
        $roles = $this->getUser()->getRoles();
        
        if (in_array("ROLE_ADMIN", $roles)) {
            return $this->redirectToRoute("admin_accueil", [
                'user' => $user
            ]);
        }

        dump($congesUser);
        dump($this->getUser());

        if ($this->getUser() AND $this->getUser()->getActivationToken()!== null) {
            return $this->redirectToRoute('app_logout');
            $this->addFlash('success', 'Veuillez activer votre compte en cliquant sur le lien qui vous a été envoyé.');
        }

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'user' => $user,
            'nb_conges' => $nb_conges,
            'nb_permissions' => $nb_permissions,
            'congesUser' => $congesUser,
            'permissionsUser' => $permissionsUser,
            'absencesUser'=> $absencesUser
        ]);

    }

    private function getUserId(): int 
    {
        $UserId = $this->getUser()?$this->getUser()->getId():'';
        return $UserId;
    }
}
