<?php

namespace App\Controller\Frontoffice;

use App\Entity\Category;
use App\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends LuneController
{


    #[Route('/gallery', name: 'gallery')]
    public function index(): Response
    {

        $category_gallery_articles = $this->entityManager->getRepository(Category::class)->getArticles([$_ENV['GALLERY_CATEGORY_ID']], $this->getParameter('locale'), true, 'dateEvent', 'DESC');

        $array_photos = array();

        foreach($category_gallery_articles as $article){


            $article_title = $article->getTitle();
            // Obtenez les photos liées à l'article
            $photos = $this->entityManager->getRepository(Media::class)->getPhotos($article);

            // Ajoutez les photos au tableau associatif avec le titre de l'article comme clé
            foreach($photos as $photo){
                $array_photos[$article_title][] = $photo;
            }
        }



        $this->data['array_photos']                 = $array_photos;
        $this->data['category_gallery_articles']    = $category_gallery_articles;
        $this->data['page_title']                   = 'Soutiens aux artistes';
        $this->data['medias_gallery']               = $this->entityManager->getRepository(Media::class)->findAll();




        return $this->render('frontoffice/gallery/index.html.twig', $this->data);
    }
}
