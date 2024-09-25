<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Notification\ContactNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/infos/', name: 'fo_infos_')]
class InfosPratiquesController extends LuneController
{

    public function __construct(EntityManagerInterface $entityManager, ContainerBagInterface $params) {
        // ! Configuration du controller :


        // Initialisation du controller.

        // Appel du constructeur du controller parent
        parent::__construct($entityManager, $params);
        // Vue liste
        $this->list_partial     =       'infos_pratiques/index.html.twig';
    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->redirect('contact');
    }


    #[Route('contact/{entrySlug}/{subjectId}', name: 'contact')]
    public function contact(Request $request, ContactNotification $notification, string $entrySlug = 'comment-venir', int $subjectId = null): Response
    {
        $this->data['page_title']                              = $this->entityManager->getRepository(Category::class)->find(39)->getTitle();
        $this->data['cat_contact']                             = $this->entityManager->getRepository(Category::class)->find(40);
        $this->data['cat_comment_venir']                       = $this->entityManager->getRepository(Category::class)->find(41);
        $this->data['articles_comment_venir']                  = $this->entityManager->getRepository(Category::class)->getArticles(array(41), $this->getParameter('locale'),1);
        $this->data['benevoles_contact']                       = $this->entityManager->getRepository(Category::class)->getArticles(array(44), $this->getParameter('locale'),1, 'ordre', 'asc');
        $this->data['contact_contact']                         = $this->entityManager->getRepository(Category::class)->getArticles(array(45), $this->getParameter('locale'),1, 'ordre', 'ASC');
        $this->data['equipe_tech_contact']                     = $this->entityManager->getRepository(Category::class)->getArticles(array(46), $this->getParameter('locale'),1, 'ordre', 'ASC');
        $this->data['question_infos']                          = $this->entityManager->getRepository(Category::class)->getArticles(array(43), $this->getParameter('locale'),1, 'ordre', 'ASC');
        $this->data['article_contact_billetterie']             = $this->entityManager->getRepository(Article::class)->find(160);
        $this->data['article_abonnement_billetterie']          = $this->entityManager->getRepository(Article::class)->find(161);
        $this->data['article_adress_billetterie']              = $this->entityManager->getRepository(Article::class)->find(159);
        $this->data['article_tarif_etudiant_billetterie']      = $this->entityManager->getRepository(Article::class)->find(162);
        $this->data['article_pass_billetterie']                = $this->entityManager->getRepository(Article::class)->find(163);
        $this->data['cat_qui_sommes']                          = $this->entityManager->getRepository(Category::class)->find(49);
        $this->data['articles_qui_sommes']                     = array_reverse(($this->entityManager->getRepository(Category::class)->getArticles(array(49), $this->getParameter('locale'),1)->toArray()));

        $entry = $this->entityManager->getRepository(Category::class)->findOneBy(['category_id' => 39, 'slug' => $entrySlug]);
        $this->data['active_entry'] = $entry;


        $this->data['active_subject'] = null;
        if($subjectId !== null)
        {
            // Sujet sélectionné pour le formulaire de contact
            $this->data['active_subject'] = Contact::SUBJECTS[$subjectId];
        }

        // Envois des catégories dans le sens d'apparition
        $this->data['sous_categories_infos']    =    $this->entityManager->getRepository(Category::class)->findBy(
            ['category_id' => 39],
            ['ordre' => 'ASC']
        );
        // CONSTANTES GENERALES
        $this->data['locale']                           = $this->getParameter('locale');

        // Création du formulaire de contact.
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        // Récupération des données du formulaire de contact.
        if($form->isSubmitted()) {

            if ($form->isValid()) {

                // Envoi du mail
                $notification->notify($contact);
                $this->addFlash('success', 'Votre email a bien été envoyé');

            }else{
                $this->addFlash('error', 'Formulaire non valide');
            }
        }else{
            //$this->addFlash('error', 'Formulaire non soumis');
        }
        // Passage du formulaire à la vue.
        $this->data['form'] = $form->createView();
        return parent::lister();
    }

}
