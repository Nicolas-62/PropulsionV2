<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/agenda/', name: 'fo_agenda_')]
class AgendaController extends FOController
{

    public function __construct(EntityManagerInterface $entityManager) {
        dump('HomeController');
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
        $this->list_partial     =       'agenda/index.html.twig';

    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        $cat_agenda_id = 3;
        // Récupération des sous catégories de la catégorie agenda
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($cat_agenda_id)->getChildrenIds();

        // Récupération des articles des sous catégories de la catégorie agenda
        $events_agenda = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->getParameter('locale'), true, 'dateEvent', 'DESC');
        foreach($events_agenda as $event) {
            dump($event->getId().' '.$event->getTitle().' '.$event->getDateEvent());
        }

        //TODO : récupérer les thèmes de catégories
        $themes_agenda = array('ATELIERS', 'CONCERTS','JEUNE PUBLIC', 'SOUTIEN AUX ARTISTES');

        $this->data['page_title']    = 'Agenda';
        $this->data['events_agenda'] = $events_agenda;
        $this->data['themes_agenda'] = $themes_agenda;

        // CONSTANTES GENERALES
        $this->data['locale']           = $this->getParameter('locale');

        return parent::lister();
    }

    #[Route('{category_id}/{title}', name: 'detail')]
    public function detail(string $title, int $category_id): Response
    {



        $cat_agenda_id = 3;
        // Récupération des sous catégories de la catégorie agenda
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($cat_agenda_id)->getChildrenIds();

        // Récupération des articles des sous catégories de la catégorie agenda
        $events_agenda = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->getParameter('locale'), true, 'dateEvent', 'DESC');
        foreach($events_agenda as $event) {
            dump($event->getId().' '.$event->getTitle().' '.$event->getDateEvent());
        }

        $cat_actu_id = 4;
        // Récupération des sous catégories de la catégorie actu
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($cat_actu_id)->getChildrenIds();

        // Récupération des articles des sous catégories de la catégorie actu
        $events_actus = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->getParameter('locale'), true, 'dateEvent', 'DESC');
        foreach($events_actus as $event) {
            dump($event->getId().' '.$event->getTitle().' '.$event->getDateEvent());
        }

        $this->data['detail_partial']       = 'frontoffice/agenda/detail.html.twig';
        $category                           = $this->entityManager->getRepository(Category::class)->findOneBy(['id' => $category_id]);
        $article                            = $this->entityManager->getRepository(Article::class)->findOneBy(['title' => $title]);
        $this->data['events_agenda']        = $events_agenda;
        $this->data['children']             = $this->entityManager->getRepository(Article::class)->findBy(['article_id' => $article->getId()]);
        $this->data['article']              = $article;
        $this->data['locale']               = $this->getParameter('locale');
        $this->data['actu_childs']          = $events_actus;
        return $this->render(
          $this->data['detail_partial'],
          $this->data);
    }


}
