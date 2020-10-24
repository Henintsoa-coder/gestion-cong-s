<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Entity\Conge;
use App\Entity\Utilisateur;
use App\Repository\CongeRepository;
use App\Form\admin\CongeAdminType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use Knp\Snappy\Pdf;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @Route("/admin_conge")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminCongeController extends AbstractController
{
    private $twig;
    private $pdf;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/", name="admin_conge_index", methods={"GET"})
     */
    public function index(CongeRepository $congeRepository): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        return $this->render('admin/conge/index.html.twig', [
            'conges' => $congeRepository->findAllDESC()
        ]);
    }


    /**
     * @Route("/{id}", name="admin_conge_show", methods={"GET"})
     */
    public function show(Conge $conge): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
       
        
        return $this->render('admin/conge/show.html.twig', [
            'conge' => $conge,
        ]);

    }

    /**
     * @Route("/edit/{id}", name="admin_conge_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Conge $conge): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        //dump($conge);
        
        /** 
         * Si le congé a déjà été accepté, on ne fait plus la différence avec le nombre de congés de l'utilisateur. 
         * nb_total_conges est donc égal à 0. :-) :-)
         * Si le congé n'a pas été accepté, on calcule le nombre de jours decimal que l'utilisateur demande.
         * Si l'état a été changé à true (si le congé a été accepté), on effectue la soustraction avec le nombre total de congés.
         * */
        if ($conge->getEtat() == true) {
            $nb_total_conges = 0;
            return $this->redirectToRoute("admin_conge_index");
            dump($conge->getEtat());
            exit();
        } elseif ($conge->getEtat() === null || $conge->getEtat() == false) {
            $timeConge = date_diff($conge->getDateDebut(), $conge->getDateFin());

            $nbJoursConges = intval($timeConge->format('%d'));

            $nbHeuresConges = intval($timeConge->h)/8;

            $nb_total_conges = $nbJoursConges + $nbHeuresConges;
        }

        $user = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['id' => $conge->getUtilisateur()]);
        //dump($user);
        
        $form = $this->createForm(CongeAdminType::class, $conge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * Si l'état de la demande de congé passe de null (ou false) à true, 
             * l'intervalle de temps représentant le congé demandé sera soustrait au nombre de congés restants.
             */
            if ($conge->getEtat() === true) {
                $user->setNbConges($user->getNbConges() - $nb_total_conges);
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Demande modifiée avec succès.');
        
            return $this->redirectToRoute('admin_conge_index');
        }

        return $this->render('admin/conge/edit.html.twig', [
            'user' => $user,
            'conge' => $conge,
            'form' => $form->createView(),
        ]);
    }

    /**
     * 
     * Permet de transformer la page web en pdf
     * 
     * @Route("/print/{id}", name="print_demande_conge", methods={"GET"})
     */
    public function print_demande(Conge $conge, \Knp\Snappy\Pdf $snappy){
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
       
       $html = $this->renderView('admin/conge/show.html.twig', [
            'conge' => $conge,
        ]); 

        $snappy->generateFromHtml(
            $html, 
            'C:\Users\Administrateur\Desktop\fichiers\Symfony 4\Demande de Conge3.pdf',  
            [
            ]
        );    
        
        $this->redirectToRoute('admin/conge/show.html.twig', [
            'conge' => $conge,
        ]);
    }

}
