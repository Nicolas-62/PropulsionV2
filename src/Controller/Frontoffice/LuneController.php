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
        parent::__construct($entityManager, $params);

        // Récupération des infos du header.
        $this->buildHeader();
        // Récupération des infos du footer.
        $this->buildFooter();

    }

    public function buildHeader(){
        $sous_categorie_ids                     =     $this->entityManager->getRepository(Category::class)->find($this->category_agenda_id)->getChildrenIds();
        $events_header                          =     $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->params->get('locale'), true, 'dateEvent', 'DESC');
        $this->data['events_header']            =     $events_header;
        $this->data['btns']                     =     $this->btns = array('GALLERY.png' => '/gallery', 'PROFIL.jpg' => 'https://billetterie.lalune.net/identification', 'COMMANDE.jpg' => 'https://billetterie.lalune.net/');
        $this->data['menu']                     =     $this->menu = array('Agenda' => 'fo_agenda_index','Actus' => 'fo_actus_index','Action Culturelle' => 'fo_actions_index','Soutiens aux artistes' => 'fo_soutiens_index','Infos Pratiques' => 'fo_infos_index');
        $this->data['lien_billetterie']         =     $this->entityManager->getRepository(Article::class)->find(184);
        $this->data['lien_billetterie_profil']  =     $this->entityManager->getRepository(Article::class)->find(183);
    }

    public function buildFooter()
    {
        // FOOTER
        $this->data['sponsors_img']       =     $this->sponsors_img = $this->entityManager->getRepository(Article::class)->findBy(['category' => 23]);
        $this->data['medias']             =     $this->medias = array('FOOTER_TEL' => 'tel:+33322978801', 'FOOTER_INSTA' => 'https://www.instagram.com/lalunedespirates/?hl=fr','FOOTER_FACEBOOK' => 'https://www.facebook.com/lalunedespirates/?locale=fr_FR');
        $this->data['mentions']           =     $this->mentions = array('Plan du site' => "/sitemap", 'FAQ' => "/faq",'Mentions légales' => "/mentions", 'CGV' => "/cgv", 'Politique de Confidentialité' => "/confidentialite", 'Espace presse' => "/espacepresse");
        $this->data['icones']             =     $this->icones = array('ARROW.jpg','BUBBLE.jpg','CADDY.jpg');
    }



    #[Route('/espacepresse', name: 'espacepresse')]
    public function Espacepresse(): Response
    {
        $this->data["categories"] = $this->entityManager->getRepository(Category::class)->findBy(['category_id'=>48]);
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

        $this->data['categories'] = $this->entityManager->getRepository(Article::class)->findBy(['category'=>27]);
        $this->data['active_entry'] = 'entry1';


        // Vue renvoyée.
        return $this->render('frontoffice/institutionnel/mentions.html.twig', $this->data);
    }

    #[Route('/faq', name: 'faq')]
    public function faq(): Response
    {
        $this->data["categories"] = $this->entityManager->getRepository(Category::class)->findBy(['category_id'=>27]);
        $this->data["articles_question"] = $this->entityManager->getRepository(Article::class)->findBy(['category'=>38]);

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
        $this->data['categories']          = $this->entityManager->getRepository(Article::class)->findBy(['category'=>28]);
        $this->data['active_entry']        = 'entry1';

        return $this->render('frontoffice/institutionnel/confidentialite.html.twig', $this->data);
    }

}


