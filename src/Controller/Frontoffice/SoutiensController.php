<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SoutiensController extends FOController
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
        $this->list_partial     =       'soutiens/index.html.twig';

    }


    #[Route('/soutiens', name: 'fo_soutiens')]
    public function index(): Response
    {



        $this->data['page_title'] = 'Soutiens aux artistes';

        // Récupération artistes accompagnés
        $this->data['accompagnes'] = $this->entityManager->getRepository(Category::class)->getGenealogy(7, $this->getParameter('locale'));
        $this->data['accompagnes_test2'] = $this->entityManager->getRepository(Category::class)->getArticles(7, $this->getParameter('locale'),1);

        $this->data['accompagnes_test'] = $this->entityManager->getRepository(Article::class)->findBy(['category' => '7']);
        $this->data['accompagnes_medias'] = ['ADAM','JONASKAY','ARYANE','DOUBLE', 'ETIENNE','KAMELECTRIC'];


        // Récupération auditions
        $auditions = $this->entityManager->getRepository(Category::class)->getGenealogy(8, $this->getParameter('locale'));

        // Récupérations des medias
        $auditions_medias =['AMBRE','CASSANDRE','EXAUBABA','LAST'];

        // Récupération event actualités
        $event_actus = $this->entityManager->getRepository(Category::class)->getGenealogy(9, $this->getParameter('locale'));

        // Récupérations des medias
        $event_actus_medias =['MUMBLE','APERO','ACTION','MAO'];

        $this->data['auditions'] = $this->entityManager->getRepository(Category::class)->getGenealogy(8, $this->getParameter('locale'));
        $this->data['auditions_medias'] = ['AMBRE','CASSANDRE','EXAUBABA','LAST'];
        $this->data['event_actus'] = $this->entityManager->getRepository(Category::class)->getGenealogy(9, $this->getParameter('locale'));
        $this->data['event_actus_medias'] = ['MUMBLE','APERO','ACTION','MAO'];

        // HEADER
        $this->data['btns']             = array('pic_icon', 'search_icon', 'profile_icon', 'menu_icon');
        $this->data['menu']             = array('Agenda' => 'fo_agenda','Actus' => 'fo_actus','Action Culturelle' => 'fo_actions','Soutiens aux artistes' => 'fo_soutiens','Infos Pratiques' => 'fo_infospratiques');

        // FOOTER
        $this->data['medias']           = array('FOOTER_TEL' => '', 'FOOTER_INSTA' => 'https://www.instagram.com/lalunedespirates/?hl=fr','FOOTER_TWITTER' => 'https://twitter.com/i/flow/login?redirect_after_login=%2Flunedespirates', 'FOOTER_FACEBOOK' => 'https://www.facebook.com/lalunedespirates/?locale=fr_FR');
        $this->data['sponsors']         = array('AMIENS_METROPOLE', 'AMIENS', 'SOMME', 'HDF', 'PREFET_HDF', 'CNM', 'SACEM', 'COPIE_PRIVEE', 'CREDIT_MUTUEL', 'FESTIVAL_INDE');
        $this->data['mentions']         = array('Plan du site' => 'plan', 'FAQ' => 'faq','Mentions légales' => 'mentions', 'CGV' => 'cgv', 'Poltique de Confidentialité' => 'confidentialite', 'Gestion des cookies' => 'cookies', 'Espace presse' => 'presse');

        // CONSTANTES GENERALES
        $this->data['locale']           = $this->getParameter('locale');

        return parent::lister();
    }
}
