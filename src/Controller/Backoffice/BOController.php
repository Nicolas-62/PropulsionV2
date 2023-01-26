<?php

namespace App\Controller\Backoffice;

use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

//#[Route('/backoffice', name: 'bo_'),  IsGranted('ROLE_ADMIN')]
class BOController extends AbstractController
{
    /**
     * @param CategoryRepository $categories
     * @return Response
     *
     * Home page, show list of categories
     */
    ##[Route('/', name: 'home')]
    public function home(CategoryRepository $categories): Response
    {
        if ( ! $this->getUser()) {
            return $this->redirectToRoute('login');
        }
        return $this->render('backoffice/home.html.twig', [
            'categories' => $categories->getParents()
        ]);
    }


    /**
     * @param Category $category
     * @return Response
     *
     * Show category detail
     */
    #[Route('/category/{id}', name: 'category')]
    public function category(Category $category): Response
    {
        return $this->render('backoffice/category.html.twig', [
            'category' => $category
        ]);
    }


}
