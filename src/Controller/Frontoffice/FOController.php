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



        $this->data['header_partial']     =     '_components/header.html.twig';
        $this->data['footer_partial']     =     '_components/footer.html.twig';
        // Vue renvoyée.
        return $this->render('frontoffice/institutionnel/mentions.html.twig', [
          "header_partial" => '_components/header.html.twig',
          "footer_partial" => '_components/footer.html.twig',
          "btns" => $this->btns,
          "menu" => $this->menu,
          "medias" => $this->medias,
          'locale'         => $this->getParameter('locale'),

        "sponsors_img" => $this->sponsors_img,
          "categories" => $categories,
          "mentions" => $this->mentions,
          'active_entry' => 'entry1',
          'infos_contact' => array('JIHANE MILADI' => 'Présidente', 'FRANÇOIS PARMENTIER' => 'Production & Vie Associative','ANTOINE GRILLON' => 'Direction & Programmation', 'VINCENT RISBOURG' => 'Soutien aux artistes', 'SANDRINE DARLOT AYMONE MIHINDOU' => 'Administration
','MARIE YACHKOURI' => 'Billetterie & Communication', 'MARTIN ROGGEMAN' => 'Régie Générale', 'KHALID MHANNAOUI' => 'Accueil','ANAÏS FRAPSAUCE MARINE SALVAT' => 'Projets Culturels & Publics', 'OLIVIER BIKS/BIBI' => 'Graphisme','JIMMY BOURBIER' => 'Communication', 'LUDO LELEU' => 'Photographe'),
        'equipe_tech' => array('Emmanuel Héreau', 'Gwennaelle Krier','Illan Lacoudre', 'Jean Maillart', 'Benoit Moritz', 'Grégory Vanheulle', 'Alexandre Verger'),
        'benevoles' => array('Alexandra', 'Antoine','Arsène', 'Beniamin', 'Bertille', 'Côme', 'Déborah', 'Elena','Elisa', 'Ewan', 'Fanny', 'Francesca', 'Gaëtan', 'Giacomo','Jules Judith', 'Laurent', 'Lisa', 'Lorea', 'Lucile', 'Manon A','Manon P', 'Marine', 'Nahelou', 'Nicolas', 'Perrine', 'Rodolphe','Romain D', 'Romain M', 'Simon', 'Valère', 'Zoé'),
        'transports' => ['EN TRAIN' => "La gare se trouve à 15 minutes à pied de l'entrée des salles !
        Avec son positionnement géographique central, Amiens se trouve à 40 minutes d'Arras, 1h05 de Paris, 1h20 de Lille, 1h25 de Rouen, 2h20 de Reims, ou encore à 3h de Bruxelles.", 'EN VOITURE' => "Amiens est au carrefour de grands axes de circulation de niveau européen : A16, A29 et à proximité des autoroutes A1 A2, A26 et A28.
        Par la voiture également, vous arriverez rapidement aux salles de concerts : 40 minutes depuis Abbeville, 50 minutes depuis Beauvais, 1h20 d'Arras, 1h20 depuis Rouen, 1h30 de Paris et de Lille.", 'PARKING' => "À Amiens, le stationnement est payant dans les rues du centre-ville de 9h à 12h30 et de 14h à 17h30 (gratuité du dimanche au lundi à 14h), et dans les zones résiden- tielles de 9h à 12h30 et de 14h à 19h (gratuité du dimanche au lundi à 14h).
        Pour mieux préparer votre venue, consultez la carte interactive du stationnement à Amiens.
        Quitte à prendre la voiture, pensez à l'option covoiturage !
        Proposez votre trajet ou cherchez-en un sur le site de notre partenaire Mobicoop.", 'À VÉLO' => "Préférez la mobilité douce ! Il est si agréable de se déplacer à vélo à Amiens...
        Vous n'avez pas de vélo ?
        Louez-en un avec le service Buscylette ou les Vélam en libre service.
        Enfin, profitez-en pour faire une belle balade autour du Patrimoine, des Hortillon- nages ou de la nature environnante !", 'EN TRANSPORT EN COMMUN' => "Profitez du réseau de bus Ametis de la ville (en plus, le samedi, les bus sont gra- tuits !), ou encore de leur service de location de vélo !
        Arrêt de bus à proximité immédiate de l'entrée des salles : Citadelle Montrescu, lignes désservies : N2, N3, 11 et L."],
        'transports_medias' => ['TRAIN', 'VOITURE', 'PARKING', 'VELO', 'BUS'],


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
