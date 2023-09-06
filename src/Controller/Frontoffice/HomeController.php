<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'fo_home_')]
class HomeController extends FOController
{

    public function __construct(EntityManagerInterface $entityManager, private ContainerBagInterface $params) {
        //dump('HomeController');
        // ! Configuration du controller :


        // Identifiants des catégories concernées.
//        $this->category_ids		=		array(46,59,69);


        // Initialisation du controller.

        // Appel du constructeur du controller parent
        parent::__construct($entityManager, $params);
        // Pas de header
        //$this->data['header_partial'] = 'home/header.html.twig';
        //$this->data['header_partial'] = '';
        //$this->data['footer_partial'] = '';
        $this->list_partial     =       'home.html.twig';
        $this->data['test'] = 'HomeController';

    }

    #[Route('home', name: 'index')]
    public function index(): Response
    {

//        $events_concert = $this->entityManager->getRepository(Category::class)->getGenealogy(12, $this->getParameter('locale'));
//        $events_jeune = $this->entityManager->getRepository(Category::class)->getGenealogy(13, $this->getParameter('locale'));
//        $events_soutiens = $this->entityManager->getRepository(Category::class)->getGenealogy(14, $this->getParameter('locale'));
//        $events_rdv = $this->entityManager->getRepository(Category::class)->getGenealogy(15, $this->getParameter('locale'));
//        $events_hors_les_murs = $this->entityManager->getRepository(Category::class)->getGenealogy(16, $this->getParameter('locale'));

        $cat_agenda_id = 3;
        // Récupération des sous catégories de la catégorie agenda
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($cat_agenda_id)->getChildrenIds();

        // Récupération des articles des sous catégories de la catégorie agenda
        $events_agenda = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->getParameter('locale'), true, 'dateEvent', 'DESC');
        foreach($events_agenda as $event) {
            //dump($event->getId().' '.$event->getTitle().' '.$event->getDateEvent());
        }

        //dump('-------------------');
        $cat_actu_id = 4;
        // Récupération des sous catégories de la catégorie actu
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($cat_actu_id)->getChildrenIds();

        // Récupération des articles des sous catégories de la catégorie actu
        $events_actus = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->getParameter('locale'), true, 'dateEvent', 'DESC');
        foreach($events_actus as $event) {
            //dump($event->getId().' '.$event->getTitle().' '.$event->getDateEvent());
        }

        $this->data['events_actus']     = $events_actus;
        $this->data['events_agenda']    = $events_agenda;
        $this->data['lien_billetterie'] = '';
        $this->data['scripts'][]        = 'home_js';
        $this->data['styles'][]         = 'home_css';

        // CONSTANTES GENERALES
        $this->data['locale']           = $this->getParameter('locale');

        return parent::lister();
    }

    #[Route('/programmation', name: 'programmation')]
    public function programmation(): Response
    {
        return $this->redirect('home');
    }

}
