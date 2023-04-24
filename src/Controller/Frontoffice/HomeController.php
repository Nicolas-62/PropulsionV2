<?php

namespace App\Controller\Frontoffice;

use App\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'fo_home_')]
class HomeController extends FOController
{

    public function __construct(EntityManagerInterface $entityManager) {

        // ! Configuration du controller :


        // Identifiants des catégories concernées.
        $this->category_ids		=		array(46,59,69);


        // Initialisation du controller.

        // Appel du constructeur du controller parent
        parent::__construct($entityManager);
        // Pas de header
        $this->data['header_partial'] = 'home/header.html.twig';
        $this->data['footer_partial'] = '';
        $this->list_partial     =       'home.html.twig';

    }

    #[Route('/home', name: 'index')]
    public function index(): Response
    {
        $this->data['lien_billetterie'] = '';
        $this->data['scripts'][] = 'home';

        return parent::lister();
    }

    #[Route('/programmation', name: 'programmation')]
    public function programmation(): Response
    {
        return $this->redirect('home');
    }

}
