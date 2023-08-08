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


    public function __construct(protected EntityManagerInterface $entityManager)
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
/*        $this->data['header_partial'] = '';
        $this->data['footer_partial'] = '';*/
        $this->list_partial           = 'actus/actus.html.twig';
        $this->datas['test']          = 'ActusController';

    }

    #[Route('/actus', name: 'fo_actus')]
    public function index(): Response
        {


        $this->data['page_title'] = 'Actus';
        $this->data['themes_actu'] = array('LA LUNE DES PIRATES', 'CONCERTS','ACTION CULTURELLE','JEUNE PUBLIC', 'SOUTIEN AUX ARTISTES');
        $this->data['actu_childs'] = $this->entityManager->getRepository(Category::class)->getGenealogy(4, $this->getParameter('locale'));
            // HEADER
        $this->data['btns']             = $this->btns = array('pic_icon', 'search_icon', 'profile_icon', 'menu_icon');
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
