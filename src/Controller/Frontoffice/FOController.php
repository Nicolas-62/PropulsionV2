<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Media;
use App\Entity\Online;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/', name: 'fo_')]
class FOController extends AbstractController
{


    public EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager  = $entityManager;
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirect('liste');
    }

    #[Route('/liste', name: 'liste')]
    public function liste(): Response
    {
        // On récupère la catégorie qui nous intéresse
        $cat = $this->entityManager->getRepository(Category::class)->find(1);
        $list = new ArrayCollection();
        $tree = $this->entityManager->getRepository(Category::class)->getGenealogy($list, 1, false);



//        dd($onlines);
        return $this->render('frontoffice/article/articles.html.twig', [
            'category' => $cat,
            'tree'     => $tree,
        ]);
    }

}
