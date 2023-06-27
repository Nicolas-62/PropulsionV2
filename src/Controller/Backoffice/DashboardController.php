<?php

namespace App\Controller\Backoffice;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Media;
use App\Entity\Mediaspec;
use App\Entity\Themes;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Controller\Admin\CategoryCrudController;
#[Route('/backoffice', name: 'bo_'),  IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    #[Route('', name: 'home')]
    public function index(): Response
    {
        // Si pas d'utilisateur connecté.
        if ( ! $this->getUser()) {
            return $this->redirectToRoute('login');
        }
        // Controleur par défaut, liste des catégories.
         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
         return $this->redirect($adminUrlGenerator->setController(CategoryCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            // Titre du backoffice
            ->setTitle('PropulsionV2')
            // Langues supportées.
            ->setLocales([
                'en' => '🇬🇧 English', // locale without custom options
                'fr' => '🇫🇷 Français',
            ])
            ;
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addWebpackEncoreEntry('backoffice')
            ;
    }



    /**
     * Configure les items du menu
     *
     * @return iterable
     */
    public function configureMenuItems(): iterable
    {
        // Lien vers le frontoffice
        yield MenuItem::linkToRoute('Aller sur le site', 'fas fa-undo', 'fo_home_index');

        // yield MenuItem::linkToDashboard('dashboard', 'fa fa-home');
        //yield MenuItem::section('Contenu','fa-solid fa-folder');

        // Liste des Catégories.
        yield MenuItem::subMenu('Categories', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Toutes les categories',  'fa-solid fa-bars', Category::class),
            MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Category::class)->setAction(Crud::PAGE_NEW),
        ]);
        // Liste des Articles.
        yield MenuItem::subMenu('Articles', 'fas fa-newspaper')->setSubItems([
            MenuItem::linkToCrud('Tous les articles', 'fas fa-newspaper', Article::class),
            MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Article::class)->setAction(Crud::PAGE_NEW),
        ]);
        // Liste des médias.
        yield MenuItem::subMenu('Medias', 'fas fa-image')->setSubItems([
            MenuItem::linkToCrud('Tous les medias',  'fa-regular fa-image', Media::class),
            MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Media::class)->setAction(Crud::PAGE_NEW),
        ]);
        // Liste des thèmes.
        yield MenuItem::subMenu('Themes', 'fas fa-palette')->setSubItems([
            MenuItem::linkToCrud('Tous les themes',  'fa-regular fa-image', Themes::class),
            MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Themes::class)->setAction(Crud::PAGE_NEW),
        ]);
        if ($this->isGranted('ROLE_ADMIN'))
        {
            // Liste des médiaspecs.
            yield MenuItem::subMenu('Mediaspecs', 'fas fa-image')->setSubItems([
                MenuItem::linkToCrud('Toutes les médiaspecs', 'fa-regular fa-image', Mediaspec::class),
                MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Mediaspec::class)->setAction(Crud::PAGE_NEW),
            ]);
        }


//        yield MenuItem::section('Administration', 'fa-solid fa-wrench');
//        yield MenuItem::linkToRoute('Profils/Droits','fa-solid fa-lock','',[]);
        if ($this->isGranted('ROLE_ADMIN'))
        {
            // Liste des utilisateurs.
            yield MenuItem::subMenu('Utilisateurs', 'fas fa-user')->setSubItems([
                MenuItem::linkToCrud('Tous les tilisateurs', 'fa-solid fa-user', User::class),
                MenuItem::linkToCrud('Ajouter', 'fas fa-plus', User::class)->setAction(Crud::PAGE_NEW),
            ]);
        }

//        yield MenuItem::linkToRoute('Préférences','fa-solid fa-gears','',[]);
//        yield MenuItem::linkToRoute('Vider le Cache','fa-solid fa-trash','',[]);
//        yield MenuItem::section('Galerie','fa-solid fa-photo-film');
//        yield MenuItem::linkToRoute('Images','fa-solid fa-image','',[]);
//        yield MenuItem::linkToRoute('Video','fa-solid fa-film','',[]);
//        yield MenuItem::section('Theme', 'fa-solid fa-palette');
//        yield MenuItem::section('Preview', 'fa-solid fa-eye');
//        yield MenuItem::section('Newsletter', 'fa-solid fa-envelope');

        // Lien de déconnexion.
        yield MenuItem::linkToLogout('Logout', 'fa fa-arrow-left');


    }

    /**
     * Configure le menu utilisateur.
     *
     * @param UserInterface $user
     * @return UserMenu
     */
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            // use the given $user object to get the user name
            ->setName($user->getEmail())
            // you can use any type of menu item, except submenus
            ->addMenuItems([
                MenuItem::section(),
                MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
            ])
            ;
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            // the first argument is the "template name", which is the same as the
            // Twig path but without the `@EasyAdmin/` prefix
            ->overrideTemplates([
                'crud/field/id' => 'backoffice/field/id.html.twig',
                'layout'        => 'backoffice/layout.html.twig'
            ])
            ;
    }
}
