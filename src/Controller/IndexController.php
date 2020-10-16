<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $user = $this->getUser()?$this->getUser()->getUserName():'';
        $nb_conges = $this->getUser()?$this->getUser()->getNbConges():'';
        $nb_permissions = $this->getUser()?$this->getUser()->getNbPermissions():'';
        
        dump($user);
        dump($nb_conges);
        dump($nb_permissions);

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'user' => $user,
            'nb_conges' => $nb_conges,
            'nb_permissions' => $nb_permissions
        ]);
    }
}
