<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/soutiens/', name: 'fo_soutiens_')]
class SoutiensController extends FOController
{




    public function __construct(EntityManagerInterface $entityManager) {
        dump('HomeController');
        // ! Configuration du controller :


        // Identifiants des catégories concernées.
        $this->category_ids		=		array(1);


        // Initialisation du controller.

        // Appel du constructeur du controller parent
        parent::__construct($entityManager);
        // Pas de header
        //$this->data['header_partial'] = 'home/header.html.twig';
        //$this->data['header_partial'] = '';
        //$this->data['footer_partial'] = '';
        $this->list_partial     =       'soutiens/index.html.twig';

    }


    #[Route('', name: 'index')]
    public function index(): Response
    {
        $this->data['page_title']           = 'Soutiens aux artistes';
        $this->data['accompagnes']          = $this->entityManager->getRepository(Category::class)->getGenealogy(7, $this->getParameter('locale'));
        $this->data['accompagnes_test2']    = $this->entityManager->getRepository(Category::class)->getArticles(7, $this->getParameter('locale'),1);
        $this->data['accompagnes_test']     = $this->entityManager->getRepository(Article::class)->findBy(['category' => '7']);
        $this->data['accompagnes_medias']   = ['ADAM','JONASKAY','ARYANE','DOUBLE', 'ETIENNE','KAMELECTRIC'];
        $this->data['auditions']            = $this->entityManager->getRepository(Category::class)->getGenealogy(8, $this->getParameter('locale'));
        $this->data['auditions_medias']     = ['AMBRE','CASSANDRE','EXAUBABA','LAST'];
        $this->data['event_actus']          = $this->entityManager->getRepository(Category::class)->getGenealogy(9, $this->getParameter('locale'));
        $this->data['event_actus_medias']   = ['MUMBLE','APERO','ACTION','MAO'];
        $this->data['locale']               = $this->getParameter('locale');

        return parent::lister();
    }



    #[Route('{title}', name: 'detail')]
    public function detail(string $title): Response
    {
        $category                           = $this->entityManager->getRepository(Category::class)->findOneBy(['id' => 7]);
        $article                            = $this->entityManager->getRepository(Article::class)->findOneBy(['title' => $title, 'category' => $category]);
        $this->data['detail_partial']       = 'frontoffice/soutiens/detail.html.twig';
        $this->data['locale']               = $this->getParameter('locale');
        $this->data['article']              = $article;
        return $this->render(
          $this->data['detail_partial'],
          $this->data);
    }

}
