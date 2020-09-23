<?php

namespace App\Controller;

use App\Entity\Presence;
use App\Form\PresenceType;
use App\Repository\PresenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/user", name="user_")
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="list")
     * @param PresenceRepository $presenceRepository
     * @return Response
     */
    public function list(PresenceRepository $presenceRepository)
    {
        $presences = $presenceRepository->createQueryBuilder('p')
            ->where('p.user = :user')
            ->orderBy('p.dateDebut')
            ->setParameter('user', $this->getUser())
            ->getQuery()->getResult();

        return $this->render('list.html.twig', [
            'presences' => $presences,
        ]);
    }

    /**
     *
     * @Route("/addPresence", name="addPresence")
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function addPresence(Request $request, EntityManagerInterface $entityManager)
    {
        $presence = new Presence();
        $presence->setDateDebut(new \DateTime('now'));
        $presence->setDateFin(new \DateTime('now'));
        $presence->setUser($this->getUser());
        $form = $this->createForm(PresenceType::class, $presence);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            if($presence->getDateDebut() > $presence->getDateFin())
            {
                $this->addFlash("danger", "La date de fin ne peut pas être inférieure à la date de début !");
            }
            else
            {
                $presence->setUser($this->getUser());
                $entityManager->persist($presence);
                $entityManager->flush();
            }
            return $this->redirectToRoute('user_list');
        }

        return $this->render('form.html.twig', [
            'formulaire' => $form->createView(),
            'titre' => 'Ajout de présence'
        ]);
    }

    /**
     * @Route("/editPresence/{id}", name="editPresence")
     *
     * @param Presence $presence
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editPresence(Presence $presence, Request $request, EntityManagerInterface $entityManager)
    {
        if($presence->getUser() === $this->getUser())
        {
            $presence->setUser($this->getUser());
            $form = $this->createForm(PresenceType::class, $presence);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                if($presence->getDateDebut() > $presence->getDateFin())
                {
                    $this->addFlash("danger", "La date de fin ne peut pas être inférieure à la date de début !");
                }
                else
                {
                    $presence->setUser($this->getUser());
                    $entityManager->persist($presence);
                    $entityManager->flush();
                }
                return $this->redirectToRoute('user_list');
            }

            return $this->render('form.html.twig', [
                'formulaire' => $form->createView(), 'titre' => 'Edition de présence'
            ]);
        }
        else
        {
            throw $this->createAccessDeniedException("Bien tenté !");
        }
    }

    /**
     * @Route("/deletePresence/{id}", name="deletePresence")
     *
     * @param Presence $presence
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePresence(Presence $presence, EntityManagerInterface $entityManager)
    {
        if($presence->getUser() === $this->getUser())
        {
            $entityManager->remove($presence);
            $entityManager->flush();
            return $this->redirectToRoute("user_list");
        }
        else
        {
            throw $this->createAccessDeniedException("Bien tenté !");
        }
    }
}
