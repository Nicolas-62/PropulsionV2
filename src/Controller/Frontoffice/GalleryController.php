<?php

namespace App\Controller\Frontoffice;

use App\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends LuneController
{


    #[Route('/gallery', name: 'app_gallery')]
    public function index(): Response
    {
        $this->data['page_title']           = 'Soutiens aux artistes';
        $this->data['medias_gallery']               = $this->entityManager->getRepository(Media::class)->findAll();
        return $this->render('frontoffice/gallery/index.html.twig', $this->data);
    }
}
