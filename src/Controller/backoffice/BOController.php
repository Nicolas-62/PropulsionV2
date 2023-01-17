<?php

namespace App\Controller\backoffice;

use App\Entity\Category;
use App\Entity\Section;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/backoffice', name: 'bo_')]
class BOController extends AbstractController
{

    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('bo_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    #[Route(path: '/logout', name: 'logout')]
    public function logout()
    {
        # throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @param CategoryRepository $categories
     * @return Response
     *
     * Home page, show list of categories
     */
    #[Route('/', name: 'home')]
    public function home(CategoryRepository $categories): Response
    {
        if ( ! $this->getUser()) {
            return $this->redirectToRoute('bo_login');
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

        return $this->render('backoffice/section.html.twig', [
            'section' => $section,
            'articles' => $articles->getArticles('section', $section->getId())
        ]);
    }


}
