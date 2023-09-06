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


    }


    #[Route('', name: 'index')]
    public function index(): Response
    {
        $this->data['page_title']           = 'Soutiens aux artistes';
        $this->data['accompagnes']          = $this->entityManager->getRepository(Category::class)->getGenealogy(7, $this->getParameter('locale'));
        $this->data['accompagnes_test2']    = $this->entityManager->getRepository(Category::class)->getArticles(array(7), $this->getParameter('locale'),1);
        $this->data['accompagnes_test']     = $this->entityManager->getRepository(Article::class)->findBy(['category' => '7']);
        $this->data['accompagnes_medias']   = ['ADAM','JONASKAY','ARYANE','DOUBLE', 'ETIENNE','KAMELECTRIC'];
        $this->data['auditions']            = $this->entityManager->getRepository(Category::class)->getGenealogy(8, $this->getParameter('locale'));
        $this->data['auditions_medias']     = ['AMBRE','CASSANDRE','EXAUBABA','LAST'];
        $this->data['event_actus']          = $this->entityManager->getRepository(Category::class)->getGenealogy(9, $this->getParameter('locale'));
        $this->data['event_actus_medias']   = ['MUMBLE','APERO','ACTION','MAO'];
        $this->data['locale']               = $this->getParameter('locale');

        return parent::lister();
    }



    #[Route('{slug}', name: 'detail')]
    public function detail(?Article $article): Response
    {
        parent::detail($article);
    }

}
