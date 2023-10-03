<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/agenda/', name: 'fo_agenda_')]
class AgendaController extends LuneController
{

    public function __construct(EntityManagerInterface $entityManager, ContainerBagInterface $params) {
        // ! Configuration du controller :
        $this->category_id  = 3;
        // Initialisation du controller.

        // Appel du constructeur du controller parent
        parent::__construct($entityManager, $params);
        // Récupération des sous catégories de la catégorie agenda
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($this->category_id)->getChildrenIds();
        // Récupération des articles des sous catégories de la catégorie agenda
        $events_agenda = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $params->get('locale'), true, 'dateEvent', 'ASC');
        $this->data['events_agenda']        = $events_agenda;

    }

    #[Route('', name: 'index')]
    public function index(): Response
    {

        $categories = $this->entityManager->getRepository(Category::class)->findBy(['category_id'=>3]);
        $this->data['categories_child_agenda'] = $categories;

        //TODO : récupérer les thèmes de catégories
        return parent::lister();
    }

    #[Route('{slug}', name: 'detail')]
    public function detail(?Article $event): Response
    {
        // Récupération des enfants de l'article
        $this->data['children']             = $this->entityManager->getRepository(Article::class)->findBy(['article_id' => $event->getId()]);
        $cat_actu_id = 4;
        // Récupération des sous catégories de la catégorie actu
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($cat_actu_id)->getChildrenIds();
        // Récupération des articles des sous catégories de la catégorie actu
        $events_actus = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->getParameter('locale'), true, 'dateEvent', 'DESC');
        $date_today = new \DateTimeImmutable();
        $date_yesterday = $date_today->modify('-1 day');

        $this->data['date_yesterday']       = $date_yesterday;
        $this->data['actu_childs']          = $events_actus;
        return parent::detail($event);
    }


    #[Route('historic/all', name: 'historic')]
    public function historic(EntityManagerInterface $entityManager, ContainerBagInterface $params): Response
    {
        $this->category_id  = 3;
        // Initialisation du controller.

        parent::__construct($entityManager, $params);
        // Appel du constructeur du controller parent
        // Récupération des sous catégories de la catégorie agenda
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($this->category_id)->getChildrenIds();

        $categories = $this->entityManager->getRepository(Category::class)->findBy(['category_id'=>3]);
        $events_agenda = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $params->get('locale'), true, 'dateEvent', 'DESC');
        $this->data['events_agenda']        = $events_agenda;
        $date_today = new \DateTimeImmutable();
        $date_yesterday = $date_today->modify('-1 day');

        $this->data['date_yesterday']       = $date_yesterday;
        return $this->render('frontoffice/agenda/historic.html.twig', $this->data);
    }


}
