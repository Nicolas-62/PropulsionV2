<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Theme;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ActusController extends FOController
{


  public function __construct(private EntityManagerInterface $entityManager)
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
    $this->data['header_partial'] = '';
    $this->data['footer_partial'] = '';
    $this->list_partial           = 'actus/actus.html.twig';
    $this->datas['test']          = 'ActusController';

  }

  #[Route('/actus', name: 'app_actus')]
    public function index(): Response
    {
      return parent::lister();
    }
}
