<?php

namespace App\Controller\Frontoffice;

use App\Entity\Category;
use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends LuneController
{
    public function __construct(
      protected EntityManagerInterface $entityManager,
      private ContainerBagInterface $params,

    )
    {
        parent::__construct($entityManager, $params);
    }


    #[Route('/gallery', name: 'gallery')]
    public function index(): Response
    {
        // Récupération des articles de la galerie
        $articles = $this->entityManager->getRepository(Category::class)->getArticles(
            [$_ENV['GALLERY_CATEGORY_ID']],
            $this->params->get('locale'),
            true,
            'dateEvent',
            'DESC'
        );
        // Tableau associatif des articles de la galerie avec leur permière photo trouvée.
        $gallery_articles = array();
        // Pour chaque article de la galerie
        foreach($articles as $index => $article) {
            $gallery_articles[$index] = array('article' => $article, 'photos' => null);
            // Obtenez les photos liées à l'article
            $photos = $this->entityManager->getRepository(Media::class)->getPhotos($article);
            if (isset($photos) && count($photos) > 0){
                // Ajout des photos à l'article
                $gallery_articles[$index]['photos'] = $photos;
            }
        }
        $this->data['gallery_articles'] = $gallery_articles;
        $this->data['cat_galery']                   = $this->entityManager->getRepository(Category::class)->findOneBy(['id' => $_ENV['GALLERY_CATEGORY_ID']]);
        $this->data['page_title']                   = 'Soutiens aux artistes';
        $this->data['medias_gallery']               = $this->entityManager->getRepository(Media::class)->findAll();




        return $this->render('frontoffice/gallery/index.html.twig', $this->data);
    }
}
