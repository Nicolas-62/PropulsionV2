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
use App\Notification\ContactNotification;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'fo_')]
class FOController extends AbstractController
{
    // Ids des categories affichées
    protected $category_ids = null;
    protected $category_id  = null;
    //Nom de la page  = nom du controller
    protected string $page = '';
    // Array des données passées au layout
    protected array $data = array();


    public function __construct(
        protected EntityManagerInterface $entityManager,
        private ContainerBagInterface $params
    )
    {
        // Datas passées à la vue.
        $this->data['styles']             = 	array(); 		// Array des feuilles de styles supplémentaires passées au layout
        $this->data['scripts']            = 	array(); 		// Array des fichiers javascript passés au layout ; chemin relatif depuis le dossier assets sans extension
        // Récupération du nom du controller courant
        $this->page = $this->getName();
        // Nom de la page = nom du controller
        $this->data['page']              = 	$this->page;
        $this->data['page_title']        = 	$this->page;
        // Nom  des vues
        $this->list_partial               =     strtolower($this->page) . '/index.html.twig'; 	// Vue de la liste
        $this->detail_partial             =     strtolower($this->page) . '/detail.html.twig'; // Vue du détail
        $this->data['header_partial']     =     '_components/header.html.twig';
        $this->data['footer_partial']     =     '_components/footer.html.twig';

        $this->data['locale']             =     $params->get('locale');
        // HEADER
        // Récupération de la SEO du site
        $this->data['seo']                =     $this->entityManager->getRepository(Config::class)->find(1)->getSeo();
        // Récupération de tous les articles
        $articles          =     $this->entityManager->getRepository(Article::class)->findAll();
        // Pour chaque article
        foreach($articles as $article) {
            // création du slug à partir du titre
            $slugify = new Slugify();
            $article->setSlug($slugify->slugify($article->getTitle()));
            // Sauvegarde de l'article
            $this->entityManager->persist($article);
            $this->entityManager->flush();
        }
    }

    /**
     * Retournes le nom du controller sans 'Controller'
     * @return string
     */
    public function getName()
    {
        return substr((new \ReflectionClass($this))->getShortName(), 0, -10);
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirect('home');
    }

    /**
     * Methode de construction du header
     *
     * @return void
     */
    public function buildHeader(){
        // A SURCHARER
    }

    /**
     * Methode de construction du footer
     * @return void
     */
    public function buildFooter(){
        // A SURCHARER
    }


    /**
     * Methode d'affichage d'un article
     *
     * @param Article|null $article
     * @return Response
     */
    public function detail(?Article $article): Response
    {
        // Si l'article n'existe pas, on redirige vers la page d'erreur
//        if (!$article) {
//            return $this->redirectToRoute('fo_index');
//        }

        // On récupère la SEO de l'article ou de ses ancètres le cas échéant
        $seo    =   $this->entityManager->getRepository(Article::class)->getSeo($article, $this->getParameter('locale'));
        // Surcharge de la seo
        // Si l'article a de la Seo et qu'elle n'est pas vide, on l'a récupère sinon on récupère la Seo de la catégorie
        if($seo != null &&  ! $seo->isEmpty()){
            $this->data['seo']  =   $seo;
        }
        $this->data['article']  =   $article;

        return $this->render($this->getParameter('app.fo_path'). $this->detail_partial, $this->data);
    }

    public function lister($champ = "ordre", $tri = "ASC", $limit = 0, $start = 0)
    {
        // Surcharge de la SEO
        // Si on a une seule categorie
        if($this->category_id != null){
            // On récupère la categorie
            $category = $this->entityManager->getRepository(Category::class)->find($this->category_id);
            // Si la catégorie existe
            if($category != null){
                // On récupère sa SEO
                $seo = $this->entityManager->getRepository(Category::class)->getSeo($category, $this->getParameter('locale'));
                // Surcharge de la seo
                if($seo != null && ! $seo->isEmpty()){
                    $this->data['seo'] = $seo;
                }
            }
        }
        // Pas utilisé pour l'instant
        // Récupération des enfants des catégories concernées.
//        $this->data['tree'] = array();
//        // Si on a une catégorie.
//        foreach($this->category_ids as $category_id){
//            $this->data['tree'][$category_id] = $this->entityManager->getRepository(Category::class)->getGenealogy($category_id, $this->getParameter('locale'));
//        }
        return $this->render($this->getParameter('app.fo_path'). $this->list_partial, $this->data);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, ContactNotification $notification): Response
    {
        // Création du formulaire de contact
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        // récupération des données soumises
        $form->handleRequest($request);
        // Controle des données
        if($form->isSubmitted() && $form->isValid()){
            $notification->notify($contact);
            $this->addFlash('success', 'Votre email a bien été envoyé');
        }else{
            $this->addFlash('error', 'Erreur');
        }
        // Vue renvoyée.
        return $this->render('frontoffice/contact/contact.html.twig', [
            'form'          => $form->createView()
        ]);
    }


    #[Route('/cgv', name: 'cgv')]
    public function cgv(): Response
    {
        $this->data['header_partial']     =     '_components/header.html.twig';
        $this->data['footer_partial']     =     '_components/footer.html.twig';
        $this->data['detail_partial']     =     'institutionnel/cgv.html.twig';
        return $this->detail(null);
    }


    #[Route('/mentions', name: 'mentions')]
    public function mentions(): Response
    {

        $categories = $this->entityManager->getRepository(Article::class)->findBy(['category'=>27]);

        $cat_agenda_id = 3;

        $sous_categorie_ids               =     $this->entityManager->getRepository(Category::class)->find($cat_agenda_id)->getChildrenIds();
        $events_header                    =     $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->params->get('locale'), true, 'dateEvent', 'DESC');


        $this->data['header_partial']     =     '_components/header.html.twig';
        $this->data['footer_partial']     =     '_components/footer.html.twig';

        // Vue renvoyée.
        return $this->render('frontoffice/institutionnel/mentions.html.twig', [
            'events_header' => $events_header,
            "header_partial" => '_components/header.html.twig',
            "footer_partial" => '_components/footer.html.twig',
            "btns" => $this->btns,
            "menu" => $this->menu,
            "medias" => $this->medias,
            'locale'         => $this->getParameter('locale'),

            "sponsors_img" => $this->sponsors_img,
            "categories" => $categories,
            "mentions" => $this->mentions,
            'active_entry' => 'entry1'
        ]);
    }

    #[Route('/faq', name: 'faq')]
    public function faq(): Response
    {

        $this->data['header_partial']     =     '_components/header.html.twig';
        $this->data['footer_partial']     =     '_components/footer.html.twig';
        // Vue renvoyée.
        return $this->render('frontoffice/institutionnel/faq.html.twig', [
            "header_partial" => '_components/header.html.twig',
            "footer_partial" => '_components/footer.html.twig',
            "btns" => $this->btns,
            "menu" => $this->menu,
            "medias" => $this->medias,
            "sponsors_img" => $this->sponsors_img,
            "mentions" => $this->mentions,
            "categories" => $this->entityManager->getRepository(Category::class)->findBy(['category_id'=>27]),

        ]);
    }

    #[Route('/sitemap', name: 'sitemap')]
    public function sitemap(): Response
    {

        $this->data['header_partial']     =     '_components/header.html.twig';
        $this->data['footer_partial']     =     '_components/footer.html.twig';
        // Vue renvoyée.
        return $this->render('frontoffice/institutionnel/sitemap.html.twig', [
            "header_partial" => '_components/header.html.twig',
            "footer_partial" => '_components/footer.html.twig',
            "btns" => $this->btns,
            "menu" => $this->menu,
            "medias" => $this->medias,
            "sponsors_img" => $this->sponsors_img,
            "mentions" => $this->mentions,

        ]);
    }

    #[Route('/confidentialite', name: 'confidentialite')]
    public function confidentialite(): Response
    {

        $this->data['header_partial']     =     '_components/header.html.twig';
        $this->data['footer_partial']     =     '_components/footer.html.twig';
        // Vue renvoyée.
        return $this->render('frontoffice/institutionnel/confidentialite.html.twig', [
            "header_partial" => '_components/header.html.twig',
            "footer_partial" => '_components/footer.html.twig',
            "btns" => $this->btns,
            "menu" => $this->menu,
            "medias" => $this->medias,
            "sponsors_img" => $this->sponsors_img,
            "mentions" => $this->mentions,

        ]);
    }


    #[Route('/gestioncookies', name: 'cookies')]
    public function GestionCookies(): Response
    {

        $this->data['header_partial']     =     '_components/header.html.twig';
        $this->data['footer_partial']     =     '_components/footer.html.twig';
        $categories = $this->entityManager->getRepository(Article::class)->findBy(['category'=>28]);

        // Vue renvoyée.
        return $this->render('frontoffice/institutionnel/gestioncookies.html.twig', [
            "categories" => $categories,
            "header_partial" => '_components/header.html.twig',
            "footer_partial" => '_components/footer.html.twig',
            "btns" => $this->btns,
            "menu" => $this->menu,
            "medias" => $this->medias,
            "sponsors_img" => $this->sponsors_img,
            "mentions" => $this->mentions,

            'locale'         => $this->getParameter('locale'),
            'active_entry' => 'entry1',

        ]);
    }


    #[Route('/espacepresse', name: 'espacepresse')]
    public function Espacepresse(): Response
    {

        $this->data['header_partial']     =     '_components/header.html.twig';
        $this->data['footer_partial']     =     '_components/footer.html.twig';
        // Vue renvoyée.
        return $this->render('frontoffice/institutionnel/espacepresse.html.twig', [
            "header_partial" => '_components/header.html.twig',
            "footer_partial" => '_components/footer.html.twig',
            "btns" => $this->btns,
            "menu" => $this->menu,
            "medias" => $this->medias,
            "sponsors_img" => $this->sponsors_img,
            "mentions" => $this->mentions,

        ]);
    }
}
