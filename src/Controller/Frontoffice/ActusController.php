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

        $this->data['page_title']  = 'Actus';
        $this->data['themes_actu'] = array('LA LUNE DES PIRATES', 'CONCERTS','ACTION CULTURELLE','JEUNE PUBLIC', 'SOUTIEN AUX ARTISTES');
        $this->data['actu_childs'] = $this->entityManager->getRepository(Category::class)->getGenealogy(4, $this->getParameter('locale'));
        $this->data['locale']      = $this->getParameter('locale');

        return parent::lister();
    }

    #[Route('{title}', name: 'detail')]
    public function detail(string $title): Response
    {
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['id' => 4]);
        $article = $this->entityManager->getRepository(Article::class)->findOneBy(['title' => $title, 'category' => $category]);
        $this->data['article']      = $article;
        $this->data['locale']       = $this->getParameter('locale');
        $this->data['actu_childs']  = $this->entityManager->getRepository(Category::class)->getGenealogy(4, $this->getParameter('locale'));
        return $this->render(
          $this->data['detail_partial'],
          $this->data);
    }
}
