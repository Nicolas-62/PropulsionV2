<?php

namespace App\Controller\Frontoffice;

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
class InfosPratiquesController extends FOController
{

    public function __construct(EntityManagerInterface $entityManager, private ContainerBagInterface $params) {
        //dump('HomeController');
        // ! Configuration du controller :


        // Identifiants des catégories concernées.
        $this->category_ids		=		array(46,59,69);


        // Initialisation du controller.

        // Appel du constructeur du controller parent
        parent::__construct($entityManager, $params);
        // Pas de header
        //$this->data['header_partial'] = 'home/header.html.twig';
        //$this->data['header_partial'] = '';
        //$this->data['footer_partial'] = '';
        $this->list_partial     =       'infos_pratiques/index.html.twig';

    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->redirect('contact');
    }


    #[Route('contact', name: 'contact')]
    public function contact(Request $request, ContactNotification $notification): Response
    {

        $this->data['transports'] = ['EN TRAIN' => "La gare se trouve à 15 minutes à pied de l'entrée des salles !
        Avec son positionnement géographique central, Amiens se trouve à 40 minutes d'Arras, 1h05 de Paris, 1h20 de Lille, 1h25 de Rouen, 2h20 de Reims, ou encore à 3h de Bruxelles.", 'EN VOITURE' => "Amiens est au carrefour de grands axes de circulation de niveau européen : A16, A29 et à proximité des autoroutes A1 A2, A26 et A28.
        Par la voiture également, vous arriverez rapidement aux salles de concerts : 40 minutes depuis Abbeville, 50 minutes depuis Beauvais, 1h20 d'Arras, 1h20 depuis Rouen, 1h30 de Paris et de Lille.", 'PARKING' => "À Amiens, le stationnement est payant dans les rues du centre-ville de 9h à 12h30 et de 14h à 17h30 (gratuité du dimanche au lundi à 14h), et dans les zones résiden- tielles de 9h à 12h30 et de 14h à 19h (gratuité du dimanche au lundi à 14h).
        Pour mieux préparer votre venue, consultez la carte interactive du stationnement à Amiens.
        Quitte à prendre la voiture, pensez à l'option covoiturage !
        Proposez votre trajet ou cherchez-en un sur le site de notre partenaire Mobicoop.", 'À VÉLO' => "Préférez la mobilité douce ! Il est si agréable de se déplacer à vélo à Amiens...
        Vous n'avez pas de vélo ?
        Louez-en un avec le service Buscylette ou les Vélam en libre service.
        Enfin, profitez-en pour faire une belle balade autour du Patrimoine, des Hortillon- nages ou de la nature environnante !", 'EN TRANSPORT EN COMMUN' => "Profitez du réseau de bus Ametis de la ville (en plus, le samedi, les bus sont gra- tuits !), ou encore de leur service de location de vélo !
        Arrêt de bus à proximité immédiate de l'entrée des salles : Citadelle Montrescu, lignes désservies : N2, N3, 11 et L."];
        $this->data['transports_medias'] = ['TRAIN', 'VOITURE', 'PARKING', 'VELO', 'BUS'];

        $this->data['infos_menu'] = array('Nous contacter' => '', 'Comment venir' => '','Tarifs et Billetterie' => '', 'Questions Fréquentes' => '');
        $this->data['active_entry'] = 'entry1';
        $this->data['infos_contact'] = array('JIHANE MILADI' => 'Présidente', 'FRANÇOIS PARMENTIER' => 'Production & Vie Associative','ANTOINE GRILLON' => 'Direction & Programmation', 'VINCENT RISBOURG' => 'Soutien aux artistes', 'SANDRINE DARLOT AYMONE MIHINDOU' => 'Administration
','MARIE YACHKOURI' => 'Billetterie & Communication', 'MARTIN ROGGEMAN' => 'Régie Générale', 'KHALID MHANNAOUI' => 'Accueil','ANAÏS FRAPSAUCE MARINE SALVAT' => 'Projets Culturels & Publics', 'OLIVIER BIKS/BIBI' => 'Graphisme','JIMMY BOURBIER' => 'Communication', 'LUDO LELEU' => 'Photographe');
        $this->data['equipe_tech'] = array('Emmanuel Héreau', 'Gwennaelle Krier','Illan Lacoudre', 'Jean Maillart', 'Benoit Moritz', 'Grégory Vanheulle', 'Alexandre Verger');
        $this->data['benevoles'] = array('Alexandra', 'Antoine','Arsène', 'Beniamin', 'Bertille', 'Côme', 'Déborah', 'Elena','Elisa', 'Ewan', 'Fanny', 'Francesca', 'Gaëtan', 'Giacomo','Jules Judith', 'Laurent', 'Lisa', 'Lorea', 'Lucile', 'Manon A','Manon P', 'Marine', 'Nahelou', 'Nicolas', 'Perrine', 'Rodolphe','Romain D', 'Romain M', 'Simon', 'Valère', 'Zoé');

        // CONSTANTES GENERALES
        $this->data['locale']           = $this->getParameter('locale');
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
