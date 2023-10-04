<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/soutiens/', name: 'fo_soutiens_')]
class SoutiensController extends LuneController
{

    public function __construct(EntityManagerInterface $entityManager, ContainerBagInterface $params) {
        // ! Configuration du controller :

        // Identifiants des catégories concernées.
        $this->category_id		=		1;

        parent::__construct($entityManager, $params);

        $sous_categorie_ids_event_actus     = $this->entityManager->getRepository(Category::class)->find(9)->getChildrenIds();
        $event_actus                        = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids_event_actus, $params->get('locale'), true, 'dateEvent', 'ASC');
        $this->event_actus                  = $event_actus;


        $sous_categorie_ids_event_actus     = $this->entityManager->getRepository(Category::class)->find(8)->getChildrenIds();
        $event_auditions                    = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids_event_actus, $params->get('locale'), true, 'dateEvent', 'ASC');
        $this->event_auditions              = $event_auditions;


        $sous_categorie_ids_event_actus     = $this->entityManager->getRepository(Category::class)->find(7)->getChildrenIds();
        $event_artistes_accomp              = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids_event_actus, $params->get('locale'), true, 'dateEvent', 'ASC');
        $this->event_artistes_accomp        = $event_artistes_accomp;

    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        $this->data['page_title']           = 'Soutiens aux artistes';
        $this->data['category_accomp']      = $this->entityManager->getRepository(Category::class)->find(7);
        $this->data['category_audition']    = $this->entityManager->getRepository(Category::class)->find(8);
        $this->data['category_event']       = $this->entityManager->getRepository(Category::class)->find(9);
        $this->data['accompagnes']          = $this->event_artistes_accomp;
        $this->data['accompagnes_test2']    = $this->entityManager->getRepository(Category::class)->getArticles(array(7), $this->getParameter('locale'),1);



        $this->data['accompagnes_test']     = $this->entityManager->getRepository(Article::class)->findBy(['category' => '7']);
        $this->data['auditions']            = $this->event_auditions;
        $this->data['event_actus']          = $this->event_actus;
        $this->data['locale']               = $this->getParameter('locale');

        return parent::lister();
    }

    #[Route('{slug}', name: 'detail')]
    public function detail(?Article $article): Response
    {
        return parent::detail($article);
    }

}
