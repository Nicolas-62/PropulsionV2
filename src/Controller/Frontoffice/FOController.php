<?php

namespace App\Controller\Frontoffice;

use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\Media;
use App\Entity\Online;
use App\Form\ContactType;
use App\Notification\ContactNotification;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'fo_')]
class FOController extends AbstractController
{
    // Datas passées à la vue.
    protected array $data;


    public function __construct(
        protected EntityManagerInterface $entityManager,
        private ContainerBagInterface $params
    )
    {
        //  Configuration du controller :

        // Manager de Doctrine.
        $this->entityManager              =     $entityManager;

        $this->category_ids               =     array();        // Identifiants des catégories concernées.

        // Datas passées à la vue.
        $this->data                       =     array();        // Array des données passées au layout
        $this->data['styles']             = 	array(); 		// Array des feuilles de styles supplémentaires passées au layout
        $this->data['scripts']            = 	array(); 		// Array des fichiers javascript passés au layout ; chemin relatif depuis le dossier assets sans extension
        $this->data['model']              = 	"/actus/"; 	// Nom du model
        $this->list_partial               =     $this->data['model'] . '/index.html.twig'; 	// Vue de la liste
        $this->data['detail_partial']     =     'frontoffice'. $this->data['model'] . '/detail.html.twig'; // Vue du détail
        $this->data['header_partial']     =     '_components/header.html.twig';
        $this->data['footer_partial']     =     '_components/footer.html.twig';
        // HEADER
        // $this->data['btns']               =     $this->btns = array('pic_icon', 'search_icon', 'profile_icon', 'menu_icon');
        $cat_agenda_id = 3;

        $sous_categorie_ids               =     $this->entityManager->getRepository(Category::class)->find($cat_agenda_id)->getChildrenIds();

        $events_header                    =     $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->params->get('locale'), true, 'dateEvent', 'DESC');

        $this->data['events_header']      =     $events_header;
        $this->data['btns']               =     $this->btns = array('pic_icon', 'profile_icon');
        $this->data['menu']               =     $this->menu = array('Agenda' => 'fo_agenda_index','Actus' => 'fo_actus_index','Action Culturelle' => 'fo_actions_index','Soutiens aux artistes' => 'fo_soutiens_index','Infos Pratiques' => 'fo_infos_index');
        // FOOTER
        $this->data['sponsors_img']       =     $this->sponsors_img = $this->entityManager->getRepository(Article::class)->findBy(['category' => 23]);
        $this->data['medias']             =     $this->medias = array('FOOTER_TEL' => '', 'FOOTER_INSTA' => 'https://www.instagram.com/lalunedespirates/?hl=fr','FOOTER_FACEBOOK' => 'https://www.facebook.com/lalunedespirates/?locale=fr_FR');
        $this->data['sponsors']           =     array('AMIENS_METROPOLE', 'AMIENS', 'SOMME', 'HDF', 'PREFET_HDF', 'CNM', 'SACEM', 'COPIE_PRIVEE', 'CREDIT_MUTUEL', 'FESTIVAL_INDE');
        $this->data['mentions']           =     $this->mentions = array('Plan du site' => "https://lune.e-systemes.fr/sitemap", 'FAQ' => "https://lune.e-systemes.fr/faq",'Mentions légales' => "https://lune.e-systemes.fr/mentions", 'CGV' => "https://lune.e-systemes.fr/cgv", 'Politique de Confidentialité' => "https://lune.e-systemes.fr/confidentialite", 'Gestion des cookies' => "https://lune.e-systemes.fr/gestioncookies", 'Espace presse' => "https://lune.e-systemes.fr/espacepresse");
        // CONSTANTES GENERALES
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirect('home');
    }

    public function lister($champ = "ordre", $tri = "ASC", $limit = 0, $start = 0)
    {


        // Récupération des enfants des catégories concernées.
        $this->data['tree'] = array();
        foreach($this->category_ids as $category_id){
            $this->data['tree'][$category_id] = $this->entityManager->getRepository(Category::class)->getGenealogy($category_id, $this->getParameter('locale'));
        }

        return $this->render($this->getParameter('app.fo_path'). $this->list_partial, $this->data);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, ContactNotification $notification): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        //dump($form->getExtraData());
        //dump($form['ville']);
        //dump($contact);

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
        // Vue renvoyée.
        return $this->render('frontoffice/institutionnel/cgv.html.twig', [
          "header_partial" => '_components/header.html.twig',
          "footer_partial" => '_components/footer.html.twig',
          "btns" => $this->btns,
          "menu" => $this->menu,
          "medias" => $this->medias,
          "sponsors_img" => $this->sponsors_img,
          "mentions" => $this->mentions,

        ]);
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
