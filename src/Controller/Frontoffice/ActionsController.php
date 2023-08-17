<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/actions/', name: 'fo_actions_')]

class ActionsController extends FOController
{
    public function __construct(EntityManagerInterface $entityManager) {
        // ! Configuration du controller :


        // Identifiants des catégories concernées.
        $this->category_ids		=		array(46,59,69);


        // Initialisation du controller.

        // Appel du constructeur du controller parent
        parent::__construct($entityManager);
        // Pas de header
        //$this->data['header_partial'] = 'home/header.html.twig';
        //$this->data['header_partial'] = '';
        //$this->data['footer_partial'] = '';
        $this->list_partial     =       'actions/index.html.twig';

    }

    #[Route('', name: 'index')]
    public function index(): Response
    {

        $cat_actu_id = 4;
        // Récupération des sous catégories de la catégorie actu
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($cat_actu_id)->getChildrenIds();

        // Récupération des articles des sous catégories de la catégorie actu
        $events_actus = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->getParameter('locale'), true, 'dateEvent', 'DESC');
        foreach($events_actus as $event) {
            //dump($event->getId().' '.$event->getTitle().' '.$event->getDateEvent());
        }


        $this->data['page_title']   = 'Action Culturelle';
        $this->data['actu_test']    = $this->entityManager->getRepository(Article::class)->findOneBy(['id' => '9']);
        $this->data['actu_childs']  = $events_actus;
        $this->data['locale']       = $this->getParameter('locale');

        return parent::lister();
    }
}
