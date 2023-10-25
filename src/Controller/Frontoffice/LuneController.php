<?php

namespace App\Controller\Frontoffice;

use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Config;
use App\Entity\Contact;
use App\Entity\Media;
use App\Entity\Online;
use App\Form\ContactType;
use App\Notification\BoNotification;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'lune_')]
class LuneController extends FOController
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        private ContainerBagInterface $params,

    )
    {
        //  Configuration du controller :
        $this->category_agenda_id                =     3;              // Identifiant de la catégorie concernée.=  3;
        $this->category_menu_ids                 =     [
            'fo_agenda_index'   => 3,
            'fo_actus_index'    => 4,
            'fo_actions_index'  => 53,
            'fo_soutiens_index' => 6,
            'fo_infos_index'    => 39
        ];
        parent::__construct($entityManager, $params);

        // Récupération des infos du header.
        $this->buildHeader();
        // Récupération des infos du footer.
        $this->buildFooter();

    }

    public function buildHeader(){


        // Récupération des articles de la galerie
        $articles = $this->entityManager->getRepository(Category::class)->getArticles([$_ENV['GALLERY_CATEGORY_ID']], $this->params->get('locale'), true, 'dateEvent', 'DESC', 4);

        // Tableau associatif des articles de la galerie avec leur permière photo trouvée.
        $gallery_articles = array();
        // Pour chaque article de la galerie
        foreach($articles as $index => $article) {
            $gallery_articles[$index] = array('article' => $article, 'photos' => null);
            // Obtenez les photos liées à l'article
            $photos = $this->entityManager->getRepository(Media::class)->getPhotos($article);
            if (isset($photos) && count($photos) > 0){
                // Ajoutez les photos à l'article
                $gallery_articles[$index]['photos'] = $photos;
            }
        }
        $this->data['gallery_articles_header'] = $gallery_articles;

        // Catégories affichées dans le menu
        $menu_category_datas = array();
        $menu_categories = $this->entityManager->getRepository(Category::class)->findBy(['id' => $this->category_menu_ids]);
        foreach($this->category_menu_ids as $url =>  $category_id) {
            // Catégories du menu
            foreach ($menu_categories as $category) {
                if($category->getId() == $category_id){
                    $category->getDatas($this->params->get('locale'));
                    $menu_category_datas[$url] = $category;
                }
            }
        }
        $this->data['menu_category_datas']          =    $menu_category_datas;

        // Programmation à venir
        $sous_categorie_ids                     =     $this->entityManager->getRepository(Category::class)->find($this->category_agenda_id)->getChildrenIds();
        $events_header                          =     $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->params->get('locale'), true, 'dateEvent', 'DESC');
        $this->data['events_header']            =     $events_header;

        $this->data['lien_billetterie']         =     $this->entityManager->getRepository(Article::class)->find(184);
        $this->data['lien_billetterie_profil']  =     $this->entityManager->getRepository(Article::class)->find(183);
    }

    public function buildFooter()
    {
        // FOOTER
        $this->data['sponsors_img']       =     $this->entityManager->getRepository(Article::class)->findBy(['category' => 23]);
        $this->data['medias']             =     $this->medias = array('FOOTER_TEL' => 'tel:+33322978801', 'FOOTER_INSTA' => 'https://www.instagram.com/lalunedespirates/?hl=fr','FOOTER_FACEBOOK' => 'https://www.facebook.com/lalunedespirates/?locale=fr_FR');
        $this->data['mentions']           =     $this->mentions = array('Plan du site' => "/sitemap", 'FAQ' => "/faq",'Mentions légales' => "/mentions", 'CGV' => "/cgv", 'Politique de Confidentialité' => "/confidentialite", 'Espace presse' => "/espacepresse");
        $this->data['icones']             =     $this->icones = array('ARROW.jpg','BUBBLE.jpg','CADDY.jpg');
    }



    #[Route('/espacepresse', name: 'espacepresse')]
    public function Espacepresse(): Response
    {


        $all_articles = $this->entityManager->getRepository(Article::class)->findBy(['category'=>48]);
        $articles = array();

        // Vérification si l'article est en ligne
        foreach($all_articles as $article){
            if($article->isOnline($this->getParameter('locale'))){
                $articles[] = $article;
            }
        }
        // Envois des articles à la vue
        $this->data['articles'] = $articles;
        $this->data['active_entry'] = 'entry1';

        // Vue renvoyée.
        return $this->render('frontoffice/institutionnel/espacepresse.html.twig', $this->data);
    }


    #[Route('/cgv', name: 'cgv')]
    public function cgv(): Response
    {
        return $this->render('frontoffice/institutionnel/cgv.html.twig', $this->data);
    }


    #[Route('/mentions', name: 'mentions')]
    public function mentions(): Response
    {

        $all_articles = $this->entityManager->getRepository(Article::class)->findBy(['category'=>27]);
        $articles = array();

        // Vérification si l'article est en ligne
        foreach($all_articles as $article){
            if($article->isOnline($this->getParameter('locale'))){
                $articles[] = $article;
            }
        }
        $this->data['articles'] = $articles;
        $this->data['active_entry'] = 'entry1';


        // Vue renvoyée.
        return $this->render('frontoffice/institutionnel/mentions.html.twig', $this->data);
    }

    #[Route('/faq', name: 'faq')]
    public function faq(): Response
    {
        $this->data["categories"] = $this->entityManager->getRepository(Category::class)->findBy(['category_id'=>27]);

        $all_articles = $this->entityManager->getRepository(Article::class)->findBy(['category'=>38]);
        $articles = array();

        // Vérification si l'article est en ligne
        foreach($all_articles as $article){
            if($article->isOnline($this->getParameter('locale'))){
                $articles[] = $article;
            }
        }



        $this->data["articles_question"] = $articles;

        // Vue renvoyée.
        return $this->render('frontoffice/institutionnel/faq.html.twig', $this->data);
    }

    #[Route('/sitemap', name: 'sitemap')]
    public function sitemap(): Response
    {
        // Vue renvoyée.
        return $this->render('frontoffice/institutionnel/sitemap.html.twig', $this->data );
    }

    #[Route('/confidentialite', name: 'confidentialite')]
    public function confidentialite(): Response
    {

        // Vue renvoyée.
        $all_articles = $this->entityManager->getRepository(Article::class)->findBy(['category'=>28]);
        $articles = array();

        // Vérification si l'article est en ligne
        foreach($all_articles as $article){
            if($article->isOnline($this->getParameter('locale'))){
                $articles[] = $article;
            }
        }

        $this->data['articles']          = $articles;
        $this->data['active_entry']        = 'entry1';

        return $this->render('frontoffice/institutionnel/confidentialite.html.twig', $this->data);
    }

}


