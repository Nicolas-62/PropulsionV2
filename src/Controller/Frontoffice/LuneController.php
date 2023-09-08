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
        private ContainerBagInterface $params
    )
    {
        //  Configuration du controller :
        $this->category_agenda_id                =     3;              // Identifiant de la catégorie concernée.=  3;
        parent::__construct($entityManager, $params);

        // Récupération des infos du header.
        $this->buildHeader();
        // Récupération des infos du footer.
        $this->buildFooter();
        dump('TEST');

    }

    public function buildHeader(){
        $sous_categorie_ids               =     $this->entityManager->getRepository(Category::class)->find($this->category_agenda_id)->getChildrenIds();
        $events_header                    =     $this->entityManager->getRepository(Category::class)->getArticles($sous_categorie_ids, $this->params->get('locale'), true, 'dateEvent', 'DESC');
        $this->data['events_header']      =     $events_header;
        $this->data['btns']               =     $this->btns = array('pic_icon', 'profile_icon');
        $this->data['menu']               =     $this->menu = array('Agenda' => 'fo_agenda_index','Actus' => 'fo_actus_index','Action Culturelle' => 'fo_actions_index','Soutiens aux artistes' => 'fo_soutiens_index','Infos Pratiques' => 'fo_infos_index');
    }

    public function buildFooter()
    {
        // FOOTER
        $this->data['sponsors_img']       =     $this->sponsors_img = $this->entityManager->getRepository(Article::class)->findBy(['category' => 23]);
        $this->data['medias']             =     $this->medias = array('FOOTER_TEL' => '', 'FOOTER_INSTA' => 'https://www.instagram.com/lalunedespirates/?hl=fr','FOOTER_FACEBOOK' => 'https://www.facebook.com/lalunedespirates/?locale=fr_FR');
        $this->data['sponsors']           =     array('AMIENS_METROPOLE', 'AMIENS', 'SOMME', 'HDF', 'PREFET_HDF', 'CNM', 'SACEM', 'COPIE_PRIVEE', 'CREDIT_MUTUEL', 'FESTIVAL_INDE');
        $this->data['mentions']           =     $this->mentions = array('Plan du site' => "/sitemap", 'FAQ' => "/faq",'Mentions légales' => "/mentions", 'CGV' => "/cgv", 'Politique de Confidentialité' => "/confidentialite", 'Gestion des cookies' => "/gestioncookies", 'Espace presse' => "/espacepresse");
    }



    #[Route('/espacepresse', name: 'espacepresse')]
    public function Espacepresse(): Response
    {

        // Vue renvoyée.
        return $this->render('frontoffice/institutionnel/espacepresse.html.twig', $this->data);
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
        $this->data['detail_partial']     =     'institutionnel/cgv.html.twig';
        return $this->detail(null);
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
        return $this->render('frontoffice/institutionnel/confidentialite.html.twig', $this->data);
    }


    #[Route('/gestioncookies', name: 'cookies')]
    public function GestionCookies(): Response
    {

        $this->data['categories']          = $this->entityManager->getRepository(Article::class)->findBy(['category'=>28]);
        $this->data['active_entry']        = 'entry1';

        // Vue renvoyée.
        return $this->render('frontoffice/institutionnel/gestioncookies.html.twig', $this->data);
    }
}


