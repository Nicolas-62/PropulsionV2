<?php

namespace App\Controller\Frontoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MaquetteController extends AbstractController
{
    #[Route('/maquette', name: 'app_maquette')]
    public function index(): Response
    {
        return $this->render('maquette/index.html.twig', [
            'controller_name' => 'MaquetteController',
        ]);
    }
}
