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
        $events_agenda = $this->entityManager->getRepository(Category::class)->getGenealogy(3, $this->getParameter('locale'));
        $themes_agenda = array('ATELIERS', 'CONCERTS','JEUNE PUBLIC', 'SOUTIEN AUX ARTISTES');

        $this->data['page_title']    = 'Agenda';
        $this->data['events_agenda'] = $events_agenda;
        $this->data['themes_agenda'] = $themes_agenda;

        // CONSTANTES GENERALES
        $this->data['locale']           = $this->getParameter('locale');

        return parent::lister();
    }

    #[Route('{title}', name: 'detail')]
    public function detail(string $title): Response
    {
        $this->data['detail_partial']       = 'frontoffice/agenda/detail.html.twig';
        $category                           = $this->entityManager->getRepository(Category::class)->findOneBy(['id' => 3]);
        $article                            = $this->entityManager->getRepository(Article::class)->findOneBy(['title' => $title, 'category' => $category]);
        $this->data['citron']               = $this->entityManager->getRepository(Article::class)->findOneBy(['id' => '39']);
        $this->data['events_agenda']        = $this->entityManager->getRepository(Category::class)->getGenealogy(3, $this->getParameter('locale'));
        $this->data['children']             = $this->entityManager->getRepository(Article::class)->findBy(['article_id' => $article->getId()]);
        $this->data['article']              = $article;
        $this->data['locale']               = $this->getParameter('locale');
        $this->data['actu_childs']          = $this->entityManager->getRepository(Category::class)->getGenealogy(4, $this->getParameter('locale'));
        return $this->render(
          $this->data['detail_partial'],
          $this->data);
    }


}
