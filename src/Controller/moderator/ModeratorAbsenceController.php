<?php

namespace App\Controller\moderator;

use App\Entity\Absence;
use App\Form\moderator\ModeratorAbsenceType;
use App\Repository\AbsenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @Route("/moderator/absence")
 * @IsGranted("ROLE_MODO")
 */
class ModeratorAbsenceController extends AbstractController
{
    /**
     * @Route("/", name="moderator_absence_index", methods={"GET"})
     */
    public function index(AbsenceRepository $absenceRepository): Response
    {
        return $this->render('moderator/absence/index.html.twig', [
            'absences' => $absenceRepository->findAllDESC(),
        ]);
    }

    /**
     * @Route("/new", name="moderator_absence_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $absence = new Absence();
        $form = $this->createForm(ModeratorAbsenceType::class, $absence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($absence);
            $entityManager->flush();

            return $this->redirectToRoute('moderator_absence_index');
        }

        return $this->render('moderator/absence/new.html.twig', [
            'absence' => $absence,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moderator_absence_show", methods={"GET"})
     */
    public function show(Absence $absence): Response
    {
        return $this->render('moderator/absence/show.html.twig', [
            'absence' => $absence,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="moderator_absence_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Absence $absence): Response
    {
        $form = $this->createForm(ModeratorAbsenceType::class, $absence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('moderator_absence_index');
        }

        return $this->render('moderator/absence/edit.html.twig', [
            'absence' => $absence,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moderator_absence_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Absence $absence): Response
    {
        if ($this->isCsrfTokenValid('delete'.$absence->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($absence);
            $entityManager->flush();
        }

        return $this->redirectToRoute('moderator_absence_index');
    }
}
