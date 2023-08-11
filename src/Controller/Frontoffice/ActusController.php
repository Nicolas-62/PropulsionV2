<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Theme;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
#[Route('/actus/', name: 'fo_actus_')]

class ActusController extends FOController
{


    public function __construct(protected EntityManagerInterface $entityManager)
    {
        // ! Configuration du controller :


        // Identifiants des catégories concernées.
        $this->category_ids		=		array(3);


        // Initialisation du controller.

        // Appel du constructeur du controller parent
        parent::__construct($entityManager);
        // ! Configuration de la page :

        // Récupération des articles de la category 3 (actus)
        //$this->data['articles'] = $this->entityManager->getRepository(Category::class)->getChildren(3);

        // Récupération des themes
        // Todo : récupérer les themes de la catégorie 3
        // $this->data['themes'] = $this->entityManager->getRepository(Theme::class)->findByArticleCategory($this->category_ids[0]);
        // ! Configuration des vues :
/*        $this->data['header_partial'] = '';
        $this->data['footer_partial'] = '';*/
        $this->datas['test']          = 'ActusController';

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
            dump($event->getId().' '.$event->getTitle().' '.$event->getDateEvent());
        }


        $this->data['page_title']  = 'Actus';
        $this->data['themes_actu'] = array('LA LUNE DES PIRATES', 'CONCERTS','ACTION CULTURELLE','JEUNE PUBLIC', 'SOUTIEN AUX ARTISTES');
        $this->data['actu_childs'] = $events_actus;
        $this->data['locale']      = $this->getParameter('locale');

        return parent::lister();
    }

    #[Route('{category_id}/{title}', name: 'detail')]
    public function detail(string $title, int $category_id): Response
    {

        $cat_actu_id = 4;
        // Récupération des sous catégories de la catégorie actu
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($cat_actu_id)->getChildrenIds();

        // Récupération des articles des sous catégories de la catégorie actu
        $events_actus = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->getParameter('locale'), true, 'dateEvent', 'DESC');
        foreach($events_actus as $event) {
            dump($event->getId().' '.$event->getTitle().' '.$event->getDateEvent());
        }



        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['id' => $category_id]);
        $article = $this->entityManager->getRepository(Article::class)->findOneBy(['title' => $title, 'category' => $category]);
        $this->data['article']      = $article;
        $this->data['locale']       = $this->getParameter('locale');
        $this->data['actu_childs']  = $events_actus;
        return $this->render(
          $this->data['detail_partial'],
          $this->data);
    }
}
