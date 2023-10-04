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
class HomeController extends LuneController
{

    public function __construct(EntityManagerInterface $entityManager, ContainerBagInterface $params) {
        //dump('HomeController');
        // ! Configuration du controller :

        // Initialisation du controller.

        // Appel du constructeur du controller parent
        parent::__construct($entityManager, $params);
        // ! Vue liste
        $this->list_partial     =       'home.html.twig';

    }

    #[Route('home', name: 'index')]
    public function index(): Response
    {
        $cat_agenda_id = 3;
        // Récupération des sous catégories de la catégorie agenda
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($cat_agenda_id)->getChildrenIds();

        // Récupération des articles des sous catégories de la catégorie agenda
        $events_agenda = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->getParameter('locale'), true, 'dateEvent', 'ASC');
        $cat_actu_id = 4;
        // Récupération des sous catégories de la catégorie actu
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($cat_actu_id)->getChildrenIds();

        // Récupération des articles des sous catégories de la catégorie actu
        $events_actus = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->getParameter('locale'), true, 'dateEvent', 'DESC');
        $date_today = new \DateTimeImmutable();
        $date_yesterday = $date_today->modify('-1 day');


        //Récupération de la catégorie Agenda pour le placeholder
        $category_agenda = $this->entityManager->getRepository(Category::class)->find(3);

        $this->data['category_agenda'] = $category_agenda;
        $this->data['date_yesterday']       = $date_yesterday;
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
