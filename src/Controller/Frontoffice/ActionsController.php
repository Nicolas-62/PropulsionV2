<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\CategoryData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/actions/', name: 'fo_actions_')]

class ActionsController extends LuneController
{
    public function __construct(EntityManagerInterface $entityManager, private ContainerBagInterface $params) {
        // ! Configuration du controller :

        // Initialisation du controller.

        // Appel du constructeur du controller parent
        parent::__construct($entityManager, $params);
        // Pas de header
        //$this->data['header_partial'] = 'home/header.html.twig';
        //$this->data['header_partial'] = '';
        //$this->data['footer_partial'] = '';
        $this->list_partial     =       'actions/index.html.twig';

    }

    #[Route('', name: 'index')]
    public function index(): Response
    {

        $cat_action_id = 53;
        // Récupération des sous catégories de la catégorie actu
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($cat_action_id)->getChildrenIds();

        // Récupération des articles des sous catégories de la catégorie actu
        $events_actus = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->getParameter('locale'), true, 'dateEvent', 'DESC');

        $cat_action = $this->entityManager->getRepository(Category::class)->find($cat_action_id);
        $articles = $this->entityManager->getRepository(Category::class)->getArticles([$cat_action_id], $this->getParameter('locale'), true, 'dateEvent', 'DESC');


        $this->data['active_entry']     = 'entry1';
        $this->data['sous_cat_actions'] = $this->entityManager->getRepository(Category::class)->findBy(['category_id' => $cat_action_id]);
        $this->data['articles']         = $articles;
        $this->data['cat_actu']         = $cat_action;
        $this->data['page_title']       = $cat_action->getTitle();
        $this->data['actu_childs']      = $events_actus;
        $this->data['locale']           = $this->getParameter('locale');

        return parent::lister();
    }

    #[Route('{title}', name: 'detail')]
    public function test($title): Response
    {


        // Récupération des sous-catégories de la catégorie actions
        $cat = $this->entityManager->getRepository(Category::class)->findOneBy(['title' => $title]);

        // Récupération des articles de la catégorie Actions PVE
        $articles = $this->entityManager->getRepository(Category::class)->getArticles([$cat->getId()], $this->getParameter('locale'), true, 'dateEvent', 'DESC');


        // Envois à la vue
        $this->data['articles']    = $articles;
        $this->data['cat']                  = $cat;

        $this->data['page_title']       = 'Projets Culturels';


        return $this->render('frontoffice/actions/detail.html.twig', $this->data );

    }
}
