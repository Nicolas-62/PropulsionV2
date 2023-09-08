<?php

namespace App\Controller\Frontoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends AbstractController
{
    #[Route('/gallery', name: 'app_gallery')]
    public function index(): Response
    {
        return $this->render('frontoffice/gallery/index.html.twig', [
            'controller_name' => 'GalleryController',
        ]);
    }
}
