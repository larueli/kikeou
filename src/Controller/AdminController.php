<?php

namespace App\Controller;

use App\Entity\Presence;
use App\Entity\Salle;
use App\Entity\TypeSalle;
use App\Entity\User;
use App\Form\FindPresenceType;
use App\Form\SalleType;
use App\Form\TypeSalleType;
use App\Repository\PresenceRepository;
use App\Repository\SalleRepository;
use App\Repository\TypeSalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @Route("/admin", name="admin_")
 * @IsGranted("ROLE_ADMIN")
 *
 * Class AdminController
 * @package App\Controller
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/find", name="find")
     * @param PresenceRepository $presenceRepository
     * @return Response
     */
    public function list(Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(FindPresenceType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $presenceRepository = $entityManager->getRepository(Presence::class);
            $query = $presenceRepository->createQueryBuilder('p');

            if($form->get('dateDebut')->getData())
            {
                $query->andWhere("p.dateDebut >= :dateDebut")->setParameter("dateDebut", $form->get('dateDebut')->getData());
            }
            if($form->get('dateFin')->getData()) {
                $query->andWhere("p.dateFin <= :dateFin")->setParameter("dateFin", $form->get('dateFin')->getData());
            }
            if($form->get('salle')->getData()) {
                $query->andWhere("p.salle = :salle")->setParameter("salle", $form->get('salle')->getData());
            }
            if($form->get('username')->getData())
            {
                $user = $entityManager->getRepository(User::class)->findOneBy(["username"=>$form->get('username')->getData()]);
                if(!is_null($user))
                    $query->andWhere("p.user = :user")->setParameter("user", $user);
            }
            $query->orderBy("p.dateDebut");
            $presences = $query->getQuery()->getResult();

            return $this->render('list.html.twig', [
                'presences' => $presences,
            ]);
        }

        return $this->render('form.html.twig', [
            'formulaire' => $form->createView(),
        ]);
    }

    /**
     * @Route("/salle/list", name="listeSalle")
     *
     * @param SalleRepository $salleRepository
     * @return Response
     */
    public function listSalle(SalleRepository $salleRepository)
    {
        $salles = $salleRepository->findAll();
        return $this->render('admin/listSalle.html.twig', ['salles'=>$salles]);
    }

    /**
     * @Route("/typeSalle/list", name="listeTypesSalle")
     *
     * @param SalleRepository $salleRepository
     * @return Response
     */
    public function listTypesSalle(TypeSalleRepository $typeSalleRepository)
    {
        $typesSalle = $typeSalleRepository->findAll();
        return $this->render('admin/listTypeSalle.html.twig', ['typesSalle'=>$typesSalle]);
    }

    /**
     * @Route("/salle/add", name="addSalle")
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function addSalle(Request $request, EntityManagerInterface $entityManager)
    {
        $salle = new Salle();
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($salle);
            $entityManager->flush();
            return $this->redirectToRoute("admin_listeSalle");
        }
        return $this->render("form.html.twig", ["formulaire"=>$form->createView()]);
    }

    /**
     * @Route("/salle/edit/{id}", name="editSalle")
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function editSalle(Salle $salle, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($salle);
            $entityManager->flush();
            return $this->redirectToRoute("admin_listeSalle");
        }
        return $this->render("form.html.twig", ["formulaire"=>$form->createView()]);
    }

    /**
     * @Route("/salle/delete/{id}", name="deleteSalle")
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function deleteSalle(Salle $salle, EntityManagerInterface $entityManager)
    {
            $entityManager->remove($salle);
            $entityManager->flush();
            return $this->redirectToRoute("admin_listeSalle");
    }

    /**
     * @Route("/typeSalle/add", name="addTypeSalle")
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function addTypeSalle(Request $request, EntityManagerInterface $entityManager)
    {
        $typeSalle = new TypeSalle();
        $form = $this->createForm(TypeSalleType::class, $typeSalle);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($typeSalle);
            $entityManager->flush();
            return $this->redirectToRoute("admin_listeSalle");
        }
        return $this->render("form.html.twig", ["formulaire"=>$form->createView()]);
    }

    /**
     * @Route("/typeSalle/edit/{id}", name="editTypeSalle")
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function editTypeSalle(TypeSalle $typeSalle, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(TypeSalleType::class, $typeSalle);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($typeSalle);
            $entityManager->flush();
            return $this->redirectToRoute("admin_listeTypesSalle");
        }
        return $this->render("form.html.twig", ["formulaire"=>$form->createView()]);
    }

    /**
     * @Route("/typeSalle/delete/{id}", name="deleteTypeSalle")
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function deleteTypeSalle(TypeSalle $typeSalle, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($typeSalle);
        $entityManager->flush();
        return $this->redirectToRoute("admin_listeTypesSalle");
    }
}
