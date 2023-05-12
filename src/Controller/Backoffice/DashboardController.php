<?php

namespace App\Controller\Backoffice;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Media;
use App\Entity\Mediaspec;
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

#[Route('/backoffice', name: 'bo_'),  IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    #[Route('', name: 'home')]
    public function index(): Response
    {
//        return parent::index();

        if ( ! $this->getUser()) {
            return $this->redirectToRoute('login');
        }

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
         return $this->redirect($adminUrlGenerator->setController(CategoryCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('PropulsionV2')
            ->setLocales([
                'en' => '🇬🇧 English', // locale without custom options
                'fr' => '🇫🇷 Français',
            ]);
    }

//    public function configureAssets(): Assets
//    {
//        return parent::configureAssets()
//            ->addWebpackEncoreEntry('backoffice')
//            ;
//    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Aller sur le site', 'fas fa-undo', 'fo_home_index');
        // yield MenuItem::linkToDashboard('dashboard', 'fa fa-home');
        yield MenuItem::section('Contenu','fa-solid fa-folder');

        yield MenuItem::subMenu('Categories', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Toutes les categories',  'fa-solid fa-bars', Category::class),
            MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Category::class)->setAction(Crud::PAGE_NEW),
        ]);

        yield MenuItem::subMenu('Articles', 'fas fa-newspaper')->setSubItems([
            MenuItem::linkToCrud('Tous les articles', 'fas fa-newspaper', Article::class),
            MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Article::class)->setAction(Crud::PAGE_NEW),
        ]);

        yield MenuItem::subMenu('Medias', 'fas fa-image')->setSubItems([
            MenuItem::linkToCrud('Tous les medias',  'fa-regular fa-image', Media::class),
            MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Media::class)->setAction(Crud::PAGE_NEW),
        ]);

        yield MenuItem::subMenu('Mediaspecs', 'fas fa-image')->setSubItems([
            MenuItem::linkToCrud('Toutes les médiaspecs',  'fa-regular fa-image', Mediaspec::class),
            MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Mediaspec::class)->setAction(Crud::PAGE_NEW),
        ]);

        yield MenuItem::section('Administration', 'fa-solid fa-wrench');
        yield MenuItem::linkToRoute('Profils/Droits','fa-solid fa-lock','',[]);


        yield MenuItem::subMenu('Utilisateurs', 'fas fa-user')->setSubItems([
            MenuItem::linkToRoute('Tous les tilisateurs','fa-solid fa-user','',[]),
            MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Mediaspec::class)->setAction(Crud::PAGE_NEW),
        ]);

        yield MenuItem::linkToRoute('Préférences','fa-solid fa-gears','',[]);
        yield MenuItem::linkToRoute('Vider le Cache','fa-solid fa-trash','',[]);
        yield MenuItem::section('Galerie','fa-solid fa-photo-film');
        yield MenuItem::linkToRoute('Images','fa-solid fa-image','',[]);
        yield MenuItem::linkToRoute('Video','fa-solid fa-film','',[]);
        yield MenuItem::section('Theme', 'fa-solid fa-palette');
        yield MenuItem::section('Preview', 'fa-solid fa-eye');
        yield MenuItem::section('Newsletter', 'fa-solid fa-envelope');
        yield MenuItem::linkToLogout('Logout', 'fa fa-arrow-left');


    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        // Usually it's better to call the parent method because that gives you a
        // user menu with some menu items already created ("sign out", "exit impersonation", etc.)
        // if you prefer to create the user menu from scratch, use: return UserMenu::new()->...
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
                'crud/field/id' => 'backoffice/field/id.html.twig'
            ])
            ;
    }
}
