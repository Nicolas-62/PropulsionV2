<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'ma_')]
class MaquetteController extends AbstractController
{


    public function __construct(
      private EntityManagerInterface $entityManager
    )
    {



        $this->data = array();
        $this->data = array('Agenda' => 'ma_agenda','Actus' => 'ma_actus','Action Culturelle' => 'ma_actions','Soutiens aux artistes' => 'ma_soutiens','Infos Pratiques' => 'ma_infospratiques');

        $this->btns = array();
        $this->btns = array('pic_icon', 'search_icon', 'profile_icon', 'menu_icon');

        $this->mentions = array();
        $this->mentions = array('Plan du site' => 'plan', 'FAQ' => 'faq','Mentions légales' => 'mentions', 'Crédits' => 'credits', 'CGV' => 'cgv', 'Poltique de Confidentialité' => 'confidentialite', 'Gestion des cookies' => 'cookies', 'Espace presse' => 'presse');

        $this->btns_footer = array();
        $this->btns_footer = array('FOOTER_SCENE', 'FOOTER_CONTACT','FOOTER_COMMANDE', 'FOOTER_NEWS');

        $this->medias = array();
        $this->medias = array('FOOTER_TEL' => '', 'FOOTER_INSTA' => 'https://www.instagram.com/lalunedespirates/?hl=fr','FOOTER_TWITTER' => 'https://twitter.com/i/flow/login?redirect_after_login=%2Flunedespirates', 'FOOTER_FACEBOOK' => 'https://www.facebook.com/lalunedespirates/?locale=fr_FR');


        // Infos pratiques
        $this->infos_menu = array();
        $this->infos_menu = array('Nous contacter' => '', 'Comment venir' => '','Tarifs et Billetterie' => '', 'Questions Fréquentes' => '');

        $this->infos_contact = array();
        $this->infos_contact = array('JIHANE MILADI' => 'Présidente', 'FRANÇOIS PARMENTIER' => 'Production & Vie Associative','ANTOINE GRILLON' => 'Direction & Programmation', 'VINCENT RISBOURG' => 'Soutien aux artistes', 'SANDRINE DARLOT AYMONE MIHINDOU' => 'Administration
','MARIE YACHKOURI' => 'Billetterie & Communication', 'MARTIN ROGGEMAN' => 'Régie Générale', 'KHALID MHANNAOUI' => 'Accueil','ANAÏS FRAPSAUCE MARINE SALVAT' => 'Projets Culturels & Publics', 'OLIVIER BIKS/BIBI' => 'Graphisme','JIMMY BOURBIER' => 'Communication', 'LUDO LELEU' => 'Photographe');


        $this->equipe_tech = array();
        $this->equipe_tech = array('Emmanuel Héreau', 'Gwennaelle Krier','Illan Lacoudre', 'Jean Maillart', 'Benoit Moritz', 'Grégory Vanheulle', 'Alexandre Verger');

        $this->benevoles = array();
        $this->benevoles = array('Alexandra', 'Antoine','Arsène', 'Beniamin', 'Bertille', 'Côme', 'Déborah', 'Elena','Elisa', 'Ewan', 'Fanny', 'Francesca', 'Gaëtan', 'Giacomo','Jules Judith', 'Laurent', 'Lisa', 'Lorea', 'Lucile', 'Manon A','Manon P', 'Marine', 'Nahelou', 'Nicolas', 'Perrine', 'Rodolphe','Romain D', 'Romain M', 'Simon', 'Valère', 'Zoé');

        $this->themes_agenda = array();
        $this->themes_agenda = array('ATELIERS', 'CONCERTS','JEUNE PUBLIC', 'SOUTIEN AUX ARTISTES');

        $this->themes_actu = array();
        $this->themes_actu = array('LA LUNE DES PIRATES', 'CONCERTS','ACTION CULTURELLE','JEUNE PUBLIC', 'SOUTIEN AUX ARTISTES');


    }

    #[Route('/maquette', name: 'maquette')]
    public function index(): Response
    {

        $events_agenda   = $this->entityManager->getRepository(Category::class)->getGenealogy(10, $this->getParameter('locale'));
        $events_homepage = $this->entityManager->getRepository(Category::class)->getGenealogy(15, $this->getParameter('locale'));


        return $this->render('maquette/index.html.twig', [
            'controller_name' => 'MaquetteController',
            'page_title' => 'Maquette',
            'data' => $this->data,
            'btns' => $this->btns,
            'mentions' => $this->mentions,
            'btns_footer' => $this->btns_footer,
            'medias' => $this->medias,
            'events_agenda' => $events_agenda,
            'events_homepage' => $events_homepage,
        ]);
    }

    #[Route('/agenda', name: 'agenda')]
    public function agenda(): Response
    {

        $events_agenda = $this->entityManager->getRepository(Category::class)->getGenealogy(10, $this->getParameter('locale'));
        $themes_agenda = $this->themes_agenda;


        return $this->render('maquette/agenda.html.twig', [
            'controller_name' => 'MaquetteController',
            'page_title' => 'Agenda',
            'data' => $this->data,
            'btns' => $this->btns,
            'mentions' => $this->mentions,
            'btns_footer' => $this->btns_footer,
            'medias' => $this->medias,
            'events_agenda' => $events_agenda,
            'themes_agenda' => $themes_agenda,
        ]);
    }

    #[Route('/actu', name: 'actus')]
    public function actus(): Response
    {

        $actu_childs = $this->entityManager->getRepository(Category::class)->getGenealogy(8, $this->getParameter('locale'));
        $actu_info = $this->entityManager->getRepository(Article::class)->findBy(['title' => 'Actu Infos']);

        $themes_actu = $this->themes_actu;




        return $this->render('maquette/actus.html.twig', [
            'controller_name' => 'MaquetteController',
            'page_title' => 'Actus',
            'data' => $this->data,
            'btns' => $this->btns,
            'mentions' => $this->mentions,
            'btns_footer' => $this->btns_footer,
            'medias' => $this->medias,
            'actu_childs' => $actu_childs,
            'actu_info' => $actu_info,
            'themes_actu' => $themes_actu,
        ]);
    }

    #[Route('/actions', name: 'actions')]
    public function pepiniere(): Response
    {


        $actu_childs = $this->entityManager->getRepository(Category::class)->getGenealogy(16, $this->getParameter('locale'));

        return $this->render('maquette/actions.html.twig', [
            'controller_name' => 'MaquetteController',
            'page_title' => 'Action Culturelle',
            'data' => $this->data,
            'btns' => $this->btns,
            'mentions' => $this->mentions,
            'btns_footer' => $this->btns_footer,
            'medias' => $this->medias,
            'actu_childs' => $actu_childs,
        ]);
    }

    #[Route('/soutiens', name: 'soutiens')]
    public function soutiens(): Response
    {
        // Récupération artistes accompagnés
        $accompagnes = $this->entityManager->getRepository(Category::class)->getGenealogy(12, $this->getParameter('locale'));

        // Récupérations des medias
        $accompagnes_medias =['ADAM','JONASKAY','ARYANE','DOUBLE', 'ETIENNE','KAMELECTRIC'];


        // Récupération auditions
        $auditions = $this->entityManager->getRepository(Category::class)->getGenealogy(13, $this->getParameter('locale'));

        // Récupérations des medias
        $auditions_medias =['AMBRE','CASSANDRE','EXAUBABA','LAST'];

        // Récupération event actualités
        $event_actus = $this->entityManager->getRepository(Category::class)->getGenealogy(14, $this->getParameter('locale'));

        // Récupérations des medias
        $event_actus_medias =['MUMBLE','APERO','ACTION','MAO'];


        $tableau_categories = array();
        $tableau_categories[0] = $accompagnes;
        $tableau_categories[1] = $auditions;



        // Envoi des données à la vue
        return $this->render('maquette/soutiens.html.twig', [
            'controller_name' => 'MaquetteController',
            'page_title' => 'Soutiens aux artistes',
            'data' => $this->data,
            'btns' => $this->btns,
            'mentions' => $this->mentions,
            'btns_footer' => $this->btns_footer,
            'medias' => $this->medias,
            'accompagnes' => $accompagnes,
            'auditions' => $auditions,
            'event_actus' => $event_actus,
            'accompagnes_medias' => $accompagnes_medias,
            'auditions_medias' => $auditions_medias,
            'event_actus_medias' => $event_actus_medias,
            'tableau_categories' => $tableau_categories,
        ]);
    }



    #[Route('/infospratiques', name: 'infospratiques')]
    public function infospratiques(): Response
    {

        $transports = array();
        $transports = ['EN TRAIN' => "La gare se trouve à 15 minutes à pied de l'entrée des salles !
        Avec son positionnement géographique central, Amiens se trouve à 40 minutes d'Arras, 1h05 de Paris, 1h20 de Lille, 1h25 de Rouen, 2h20 de Reims, ou encore à 3h de Bruxelles.", 'EN VOITURE' => "Amiens est au carrefour de grands axes de circulation de niveau européen : A16, A29 et à proximité des autoroutes A1 A2, A26 et A28.
        Par la voiture également, vous arriverez rapidement aux salles de concerts : 40 minutes depuis Abbeville, 50 minutes depuis Beauvais, 1h20 d'Arras, 1h20 depuis Rouen, 1h30 de Paris et de Lille.", 'PARKING' => "À Amiens, le stationnement est payant dans les rues du centre-ville de 9h à 12h30 et de 14h à 17h30 (gratuité du dimanche au lundi à 14h), et dans les zones résiden- tielles de 9h à 12h30 et de 14h à 19h (gratuité du dimanche au lundi à 14h).
        Pour mieux préparer votre venue, consultez la carte interactive du stationnement à Amiens.
        Quitte à prendre la voiture, pensez à l'option covoiturage !
        Proposez votre trajet ou cherchez-en un sur le site de notre partenaire Mobicoop.", 'À VÉLO' => "Préférez la mobilité douce ! Il est si agréable de se déplacer à vélo à Amiens...
        Vous n'avez pas de vélo ?
        Louez-en un avec le service Buscylette ou les Vélam en libre service.
        Enfin, profitez-en pour faire une belle balade autour du Patrimoine, des Hortillon- nages ou de la nature environnante !", 'EN TRANSPORT EN COMMUN' => "Profitez du réseau de bus Ametis de la ville (en plus, le samedi, les bus sont gra- tuits !), ou encore de leur service de location de vélo !
        Arrêt de bus à proximité immédiate de l'entrée des salles : Citadelle Montrescu, lignes désservies : N2, N3, 11 et L."];

        $transports_medias = array();
        $transports_medias = ['TRAIN', 'VOITURE', 'PARKING', 'VELO', 'BUS'];



        return $this->render('maquette/infospratiques.html.twig', [
            'controller_name' => 'MaquetteController',
            'page_title' => 'Infos Pratiques',
            'data' => $this->data,
            'btns' => $this->btns,
            'mentions' => $this->mentions,
            'btns_footer' => $this->btns_footer,
            'medias' => $this->medias,
            'infos_menu' => $this->infos_menu,
            'infos_contact' => $this->infos_contact,
            'equipe_tech' => $this->equipe_tech,
            'benevoles' => $this->benevoles,
            'transports' => $transports,
            'transports_medias' => $transports_medias,
            'active_entry' => 'entry1',
        ]);
    }


    // MENTIONS LEGALES

    #[Route('/plan', name: 'plan')]
    public function plandusite(): Response
    {
        return $this->render('maquette/plan.html.twig', [
          'controller_name' => 'MaquetteController',
          'page_title' => 'Plan du site',
          'data' => $this->data,
          'btns' => $this->btns,
          'mentions' => $this->mentions,
          'btns_footer' => $this->btns_footer,
          'medias' => $this->medias,
        ]);
    }


    #[Route('/faq', name: 'faq')]
    public function faq(): Response
    {
        return $this->render('maquette/faq.html.twig', [
          'controller_name' => 'MaquetteController',
          'page_title' => 'FAQ',
          'data' => $this->data,
          'btns' => $this->btns,
          'mentions' => $this->mentions,
          'btns_footer' => $this->btns_footer,
          'medias' => $this->medias,
        ]);
    }


    #[Route('/mentions', name: 'mentions')]
    public function mentions(): Response
    {
        return $this->render('maquette/mentions.html.twig', [
          'controller_name' => 'MaquetteController',
          'page_title' => 'Mentions légales',
          'data' => $this->data,
          'btns' => $this->btns,
          'mentions' => $this->mentions,
          'btns_footer' => $this->btns_footer,
          'medias' => $this->medias,
        ]);
    }


    #[Route('/cgv', name: 'cgv')]
    public function cgv(): Response
    {
        return $this->render('maquette/mentions.html.twig', [
          'controller_name' => 'MaquetteController',
          'page_title' => 'Mentions légales',
          'data' => $this->data,
          'btns' => $this->btns,
          'mentions' => $this->mentions,
          'btns_footer' => $this->btns_footer,
          'medias' => $this->medias,
        ]);
    }


    #[Route('/confidentialite', name: 'confidentialite')]
    public function confidentialite(): Response
    {
        return $this->render('maquette/confidentialite.html.twig', [
          'controller_name' => 'MaquetteController',
          'page_title' => 'Politique de confidentialité',
          'data' => $this->data,
          'btns' => $this->btns,
          'mentions' => $this->mentions,
          'btns_footer' => $this->btns_footer,
          'medias' => $this->medias,
        ]);
    }
    #[Route('/credits', name: 'credits')]
    public function credits(): Response
    {
        return $this->render('maquette/credits.html.twig', [
          'controller_name' => 'MaquetteController',
          'page_title' => 'Crédits',
          'data' => $this->data,
          'btns' => $this->btns,
          'mentions' => $this->mentions,
          'btns_footer' => $this->btns_footer,
          'medias' => $this->medias,
        ]);
    }


    #[Route('/cookies', name: 'cookies')]
    public function cookies(): Response
    {
        return $this->render('maquette/cookies.html.twig', [
          'controller_name' => 'MaquetteController',
          'page_title' => 'Gestion des cookies',
          'data' => $this->data,
          'btns' => $this->btns,
          'mentions' => $this->mentions,
          'btns_footer' => $this->btns_footer,
          'medias' => $this->medias,
        ]);
    }

    #[Route('/presse', name: 'presse')]
    public function presse(): Response
    {
        return $this->render('maquette/presse.html.twig', [
          'controller_name' => 'MaquetteController',
          'page_title' => 'Espace presse',
          'data' => $this->data,
          'btns' => $this->btns,
          'mentions' => $this->mentions,
          'btns_footer' => $this->btns_footer,
          'medias' => $this->medias,
        ]);
    }


    #[Route('/maquetteprogrammation', name: 'programmation')]
    public function programmation(): Response
    {

        $events_agenda = $this->entityManager->getRepository(Category::class)->getGenealogy(10, $this->getParameter('locale'));
        return $this->render('maquette/programmation.html.twig', [
            'controller_name' => 'MaquetteController',
            'page_title' => 'Espace presse',
            'data' => $this->data,
            'btns' => $this->btns,
            'mentions' => $this->mentions,
            'btns_footer' => $this->btns_footer,
            'medias' => $this->medias,
            'events_agenda' => $events_agenda,

        ]);
    }

}
