<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/actions/', name: 'fo_actions_')]

class ActionsController extends FOController
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
        $this->list_partial     =       'actions/index.html.twig';

    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        $this->data['page_title']   = 'Action Culturelle';
        $this->data['actu_test']    = $this->entityManager->getRepository(Article::class)->findOneBy(['id' => '9']);
        $this->data['actu_childs']  = $this->entityManager->getRepository(Category::class)->getGenealogy(4, $this->getParameter('locale'));
        $this->data['locale']       = $this->getParameter('locale');

        return parent::lister();
    }
}
