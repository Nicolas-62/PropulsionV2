<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Online;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/page/', name: 'fo_page_')]
class PageController extends LuneController
{


    public function __construct(protected EntityManagerInterface $entityManager, ContainerBagInterface $params,
                                protected RequestStack $requestStack)
    {
        // ! Configuration du controller :


        // Identifiants des catégories concernées.
        $this->category_id = 22;

        // Initialisation du controller.

        // Appel du constructeur du controller parent
        parent::__construct($entityManager, $params);
        // ! Configuration de la page :
    }


    #[Route('{hash}', name: 'detail')]
    public function detail(?Article $event): Response
    {

        if($event != null){
            $event_page = $this->entityManager->getRepository(Article::class)->findOneBy(['id' => $event->getId()]);
            $online = $this->entityManager->getRepository(Online::class)->findOneBy(['article' => $event->getId(),'language' => 2]);
            $session        = $this->requestStack->getSession();
            $preview        = $session->get('preview');

            $this->data['online']             = $online;
            if($online->isOnline() == true or $preview ){
                $this->data['event']             = $event_page;
            }else{
                $this->data['event']             = null;
            }




        }else{
            $this->data['event']             = null;
        }
        return parent::detail($event);
    }
}
