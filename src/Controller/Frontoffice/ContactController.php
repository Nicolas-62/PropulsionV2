<?php

namespace App\Controller\Frontoffice;

use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\Media;
use App\Form\ContactType;
use App\Notification\ContactNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'fo_contact_')]
class ContactController extends LuneController
{
    #[Route('contact', name: 'contact')]
    public function contact(Request $request, ContactNotification $notification): Response
    {



        $categories = $this->entityManager->getRepository(Category::class)->findBy(['category_id'=>3]);
        // Récupération de la date d'hier pour la comparaison de dates
        $date_today = new \DateTimeImmutable();
        $date_yesterday = $date_today->modify('-1 day');
        //Récupération de la catégorie Agenda pour le placeholder
        $category_agenda = $this->entityManager->getRepository(Category::class)->find(3);
        $category_contact = $this->entityManager->getRepository(Category::class)->find(40);

        $this->data['cat_contact']                             = $this->entityManager->getRepository(Category::class)->find(40);
        $this->data['active_entry'] = "entry1";
        $this->data['categorie_contact'] =$category_contact;
        $this->data['category_agenda'] = $category_agenda;
        $this->data['date_yesterday']          = $date_yesterday;
        $this->data['categories_child_agenda'] = $categories;
        $this->data['contact_contact']                         = $this->entityManager->getRepository(Category::class)->getArticles(array(45), $this->getParameter('locale'),1, 'ordre', 'ASC');
        $this->data['equipe_tech_contact']                     = $this->entityManager->getRepository(Category::class)->getArticles(array(46), $this->getParameter('locale'),1, 'ordre', 'ASC');
        $this->data['benevoles_contact']                       = $this->entityManager->getRepository(Category::class)->getArticles(array(44), $this->getParameter('locale'),1);

        // Création du formulaire de contact.
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        // Récupération des données du formulaire de contact.
        if($form->isSubmitted()) {
            if ($form->isValid()) {
                // Récupération du destinataire en fonction du seujet choisi.
                $destinataire_emails = Contact::getEmailBySubjectLabel($contact->getSubject());
                if($destinataire_emails){
                    $contact->setEmail($destinataire_emails);
                    // Envoi du mail
                    $notification->notify($contact);
                    $this->addFlash('success', 'Votre email a bien été envoyé');
                }else{
                    $this->addFlash('error', "Impossible de récupérer l'adresse du destinataire");
                }
            }else{
                $this->addFlash('error', 'Formulaire non valide');
            }
        }else{
            //$this->addFlash('error', 'Formulaire non soumis');
        }
        // Passage du formulaire à la vue.
        $this->data['form'] = $form->createView();

        //TODO : récupérer les thèmes de catégories
        return parent::lister();
    }

}
