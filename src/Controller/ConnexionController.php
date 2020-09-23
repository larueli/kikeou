<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConnexionController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        return $this->redirectToRoute("user_list");
    }

    /**
     * @Route("/connexion/cas", name="connexion_cas")
     */
    public function cas()
    {
        return $this->redirectToRoute("user_list");
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        return $this->redirect("https://utt.fr");
    }


}
