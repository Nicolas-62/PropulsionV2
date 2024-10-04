<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Theme;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
#[Route('/actus/', name: 'fo_actus_')]

class ActusController extends LuneController
{


    public function __construct(protected EntityManagerInterface $entityManager, ContainerBagInterface $params, protected RequestStack $requestStack)
    {
        // ! Configuration du controller :


        // Identifiants des catégories concernées.
        $this->category_id = 4;

        // Initialisation du controller.

        // Appel du constructeur du controller parent
        parent::__construct($entityManager, $params, $requestStack);
        // ! Configuration de la page :
        $this->data['page_title']           = $this->entityManager->getRepository(Category::class)->find($this->category_id)->getTitle();

    }

    #[Route('', name: 'index')]
    public function index(): Response
    {

        // Récupération des sous catégories de la catégorie actu
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($this->category_id)->getChildrenIds();

        // Récupération des articles des sous catégories de la catégorie actu
        $events_actus = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->getParameter('locale'), true, 'createdAt', 'DESC');

        $categories = $this->entityManager->getRepository(Category::class)->findBy(['category_id'=>4]);
        $category_actu = $this->entityManager->getRepository(Category::class)->find($this->category_id);
        $this->data['category_actu'] = $category_actu;
        $this->data['categories_actu'] = $categories;
        $this->data['actu_childs'] = $events_actus;

        return parent::lister();
    }

    #[Route('{slug}', name: 'detail')]
    public function detail(?Article $actus): Response
    {
        $categories = $this->entityManager->getRepository(Category::class)->findBy(['category_id'=>4]);

        $category_actu = $this->entityManager->getRepository(Category::class)->find($this->category_id);
        $category_actu = $this->entityManager->getRepository(Category::class)->find($this->category_id);
        $this->data['categories_actu'] = $categories;
        $this->data['category_actu'] = $category_actu;
        // Récupération des sous catégories de la catégorie actu
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($this->category_id)->getChildrenIds();
        // Récupération des articles des sous catégories de la catégorie actu
        $events_actus = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->getParameter('locale'), true, 'dateEvent', 'DESC');
        $this->data['actu_childs']  = $events_actus;
        return parent::detail($actus);
    }
}
