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
        $this->data['menu']               =     array('Agenda' => 'fo_agenda_index','Actus' => 'fo_actus_index','Action Culturelle' => 'fo_actions_index','Soutiens aux artistes' => 'fo_soutiens_index','Infos Pratiques' => 'fo_infos_index');
        // FOOTER
        $this->data['sponsors_img']       =     $this->entityManager->getRepository(Article::class)->findBy(['category' => 23]);
        $this->data['medias']             =     array('FOOTER_TEL' => '', 'FOOTER_INSTA' => 'https://www.instagram.com/lalunedespirates/?hl=fr','FOOTER_FACEBOOK' => 'https://www.facebook.com/lalunedespirates/?locale=fr_FR');
        $this->data['sponsors']           =     array('AMIENS_METROPOLE', 'AMIENS', 'SOMME', 'HDF', 'PREFET_HDF', 'CNM', 'SACEM', 'COPIE_PRIVEE', 'CREDIT_MUTUEL', 'FESTIVAL_INDE');
        $this->data['mentions']           =     array('Plan du site' => 'plan', 'FAQ' => 'faq','Mentions légales' => 'mentions', 'CGV' => 'cgv', 'Poltique de Confidentialité' => 'confidentialite', 'Gestion des cookies' => 'cookies', 'Espace presse' => 'presse');
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
}
