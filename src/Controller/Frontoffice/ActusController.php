<?php

namespace App\Controller\Frontoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ActusController extends FOController
{


  public function __construct(EntityManagerInterface $entityManager)
  {
    // ! Configuration du controller :


    // Identifiants des catégories concernées.
    $this->category_ids		=		array(3);


    // Initialisation du controller.

    // Appel du constructeur du controller parent
    parent::__construct($entityManager);

    $this->data['header_partial'] = '';
    $this->data['footer_partial'] = '';
    $this->list_partial           =       'actus/actus.html.twig';
    $this->datas['test']          = 'ActusController';

  }

  #[Route('/actus', name: 'app_actus')]
    public function index(): Response
    {
      return parent::lister();
    }
}
