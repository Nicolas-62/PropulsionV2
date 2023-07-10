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


    }

    #[Route('/maquette', name: 'maquette')]
    public function index(): Response
    {
        return $this->render('maquette/index.html.twig', [
            'controller_name' => 'MaquetteController',
            'page_title' => 'Maquette',
            'data' => $this->data,
            'btns' => $this->btns,
            'mentions' => $this->mentions,
            'btns_footer' => $this->btns_footer,
            'medias' => $this->medias,
        ]);
    }

    #[Route('/agenda', name: 'agenda')]
    public function agenda(): Response
    {
        return $this->render('maquette/agenda.html.twig', [
            'controller_name' => 'MaquetteController',
            'page_title' => 'Agenda',
            'data' => $this->data,
            'btns' => $this->btns,
            'mentions' => $this->mentions,
            'btns_footer' => $this->btns_footer,
            'medias' => $this->medias,
        ]);
    }

    #[Route('/actu', name: 'actus')]
    public function actus(): Response
    {

        $actu_childs = $this->entityManager->getRepository(Category::class)->getGenealogy(8, $this->getParameter('locale'));

        $actu_info = $this->entityManager->getRepository(Article::class)->findBy(['title' => 'Actu Infos']);




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
        ]);
    }

    #[Route('/actions', name: 'actions')]
    public function pepiniere(): Response
    {
        return $this->render('maquette/actions.html.twig', [
            'controller_name' => 'MaquetteController',
            'page_title' => 'Action Culturelle',
            'data' => $this->data,
            'btns' => $this->btns,
            'mentions' => $this->mentions,
            'btns_footer' => $this->btns_footer,
            'medias' => $this->medias,
        ]);
    }

    #[Route('/soutiens', name: 'soutiens')]
    public function soutiens(): Response
    {
        return $this->render('maquette/soutiens.html.twig', [
            'controller_name' => 'MaquetteController',
            'page_title' => 'Soutiens aux artistes',
            'data' => $this->data,
            'btns' => $this->btns,
            'mentions' => $this->mentions,
            'btns_footer' => $this->btns_footer,
            'medias' => $this->medias,
        ]);
    }



    #[Route('/infospratiques', name: 'infospratiques')]
    public function infospratiques(): Response
    {
        return $this->render('maquette/infospratiques.html.twig', [
            'controller_name' => 'MaquetteController',
            'page_title' => 'Infos Pratiques',
            'data' => $this->data,
            'btns' => $this->btns,
            'mentions' => $this->mentions,
            'btns_footer' => $this->btns_footer,
            'medias' => $this->medias,
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


}
