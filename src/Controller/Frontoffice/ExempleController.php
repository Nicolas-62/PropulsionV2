<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/exemple', name: 'exemple')]
class ExempleController extends AbstractController
{


    public EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager  = $entityManager;
    }

    #[Route('/exemples', name: 'exemples')]
    public function index(): Response
    {
        $articles = $this->entityManager->getRepository(Article::class)->findBy(['category' => 1]);
        return $this->render('frontoffice/exemple/exemples.html.twig', [
            'controller_name' => 'FOController',
            'articles' => $articles,
        ]);
    }


}
