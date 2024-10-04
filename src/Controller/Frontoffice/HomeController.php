<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use App\Twig\AppExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\TwigFunction;

#[Route('/', name: 'fo_home_')]
class HomeController extends LuneController
{

    public function __construct(EntityManagerInterface $entityManager, ContainerBagInterface $params) {
        //dump('HomeController');
        // ! Configuration du controller :

        // Initialisation du controller.

        // Appel du constructeur du controller parent
        parent::__construct($entityManager, $params);
        // ! Vue liste
        $this->list_partial     =       'home.html.twig';

    }

    #[Route('rdd', name: 'rdd')]
    public function rdd(){
        // Maj ordre des articles des catégories
        echo 'méthode de reprise'; exit();

        // Reset de l'ordre des articles d'une catégorie
        $categories = $this->entityManager->getRepository(Category::class)->findBy(['id'=> [43,44,45,46]]);
//        $categories = $this->entityManager->getRepository(Category::class)->findBy(['id'=> '43']);

        foreach($categories as $category){
            dump($category->id);

            $articles = $this->entityManager->getRepository(Article::class)->findBy(['category'=> $category], ['id' => 'ASC']);
            foreach($articles as $index =>  $article){
                dump($article->id);
                $article->setOrdre($index+1);
                $this->entityManager->persist($article);
            }

        }
        $this->entityManager->flush();

        return new Response('end');
    }

    #[Route('home', name: 'index')]
    public function index(): Response
    {
        $cat_agenda_id = 3;
        // Récupération des sous catégories de la catégorie agenda
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($cat_agenda_id)->getChildrenIds();

        // Récupération des articles des sous catégories de la catégorie agenda
        $events_agenda = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->getParameter('locale'), true, 'dateEvent', 'ASC');
        $twig = $this->container->get('twig')->getExtension(AppExtension::class);
        $date_today = new \DateTimeImmutable();
        $current_date = $date_today->format('Y-m-d H:i:s');
        // Récupération des concerts à venir
        $events_agenda = $events_agenda->filter(function($event_agenda) use($current_date, $twig) {
            if($twig->getDatetimeEvent($event_agenda->getDateEvent(), $event_agenda->getDatetimeEvent()) >  $current_date){
                return true;
            }
            return false;
        });
        $cat_actu_id = 4;
        // Récupération des sous catégories de la catégorie actu
        $sous_categorie_ids = $this->entityManager->getRepository(Category::class)->find($cat_actu_id)->getChildrenIds();

        // Récupération des articles des sous catégories de la catégorie actu
        $events_actus = $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->getParameter('locale'), true, 'dateEvent', 'DESC');

        //Récupération de la catégorie Agenda pour le placeholder
        $category_agenda = $this->entityManager->getRepository(Category::class)->find(3);

        $this->data['category_agenda'] = $category_agenda;
        $this->data['events_actus']     = $events_actus;
        $this->data['events_agenda']    = $events_agenda;
        $this->data['lien_billetterie'] = '';
        $this->data['scripts'][]        = 'home_js';
        $this->data['styles'][]         = 'home_css';

        // CONSTANTES GENERALES
        $this->data['locale']           = $this->getParameter('locale');

        return parent::lister();
    }

    #[Route('/programmation', name: 'programmation')]
    public function programmation(): Response
    {
        return $this->redirect('home');
    }

}
