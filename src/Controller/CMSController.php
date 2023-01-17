<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\CategoryRepository;
use App\Repository\ArticleRepository;
use App\Entity\Category;
use App\Entity\Section;


class CMSController extends AbstractController
{

    /**
     * @param CategoryRepository $categories
     * @return Response
     *
     * Home page, show list of categories
     */
    #[Route('/', name: 'home')]
    public function home(CategoryRepository $categories): Response
    {
        return $this->render('cms/home.html.twig', [
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
        return $this->render('cms/category.html.twig', [
            'category' => $category
        ]);
    }


    /**
     * @param Section $section
     * @param ArticleRepository $articles
     * @return Response
     *
     * Show section detail
     */
    #[Route('/section/{id}', name: 'section')]
    public function section(Section $section, ArticleRepository $articles): Response
    {

        return $this->render('cms/section.html.twig', [
            'section' => $section,
            'articles' => $articles->getArticles('section', $section->getId())
        ]);
    }


}
