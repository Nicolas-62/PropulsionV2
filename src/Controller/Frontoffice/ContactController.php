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
        // Controller pas utilisé ici, voi Infos_pratiques/contact
        throw $this->createNotFoundException('Page introuvable');
        // Création du formulaire de contact.
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        // Récupération des données du formulaire de contact.
        if($form->isSubmitted()) {
            if ($form->isValid()) {
                $notification->notify($contact);
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
