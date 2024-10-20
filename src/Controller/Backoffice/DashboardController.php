<?php

namespace App\Controller\Backoffice;

use App\Entity\Classe;
use App\Entity\Config;
use App\Entity\Eleve;
use App\Entity\Matiere;
use App\Entity\Professeur;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatableMessage;

#[Route('/', name: 'bo_')]
class DashboardController extends AbstractDashboardController
{
    private $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    #[Route('', name: 'home')]
    public function index(): Response
    {
        // Si pas d'utilisateur connecté.
        if (!$this->getUser()) {
            return $this->redirectToRoute('user_login');
        }
        // Controleur par défaut, liste des catégories.
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ClasseCrudController::class)->generateUrl());

    }

    #[Route('/clearCache', name: 'clear_cache')]
    public function clearCache(CacheItemPoolInterface $cache): Response
    {
        $cache->clear();
        try {
            // Suppression du dossier
            //$filesystem->remove($new_cache_path);
            $this->session->getFlashBag()->add('success', new TranslatableMessage('content_admin.flash_message.clearCache') );
        }catch(Exception $e){
            $this->session->getFlashBag()->add('error', $e->getMessage());
        }


        return $this->redirectToRoute('bo_home');
    }

    /**
     * Permet de voir dans le frontoffice les éléments hors ligne.
     * @return Response
     */
    #[Route('/preview', name: 'toggle_preview')]
    public function togglePreview(): Response
    {
        $preview = $this->session->get('preview');
        if ($preview == null) {
            $this->session->set('preview', true);
        } else {
            $this->session->remove('preview');
        }

        return $this->redirectToRoute('bo_home');
    }




    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            // Titre du backoffice
            ->setTitle($_ENV['SITE'])
            ->renderContentMaximized();
    }

    /**
     * @return Assets
     * Configuration des assets.
     */
    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addWebpackEncoreEntry('backoffice');
    }


    /**
     * Configure les items du menu
     *
     * @return iterable
     */
    public function configureMenuItems(): iterable
    {
        // Dossier avec etapes

        // Si l'utilisateur est admin
        if ($this->isGranted('ROLE_ADMIN')) {
            // Liste des Classes.
            yield MenuItem::linkToCrud('Classes', 'fas fa-chalkboard', Classe::class)->setController(ClasseCrudController::class);
            // Liste des Professeurs.
            yield MenuItem::linkToCrud('Professeurs', 'fas fa-chalkboard-teacher', Professeur::class)->setController(ProfesseurCrudController::class);
            // Liste des Matières.
            yield MenuItem::linkToCrud('Matières', 'fas fa-university', Matiere::class)->setController(MatiereCrudController::class);
            // Liste des Eleves.
            yield MenuItem::linkToCrud('Eleves', 'fas fa-user', Eleve::class)->setController(EleveCrudController::class);
        }
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
            // !! lien de déconnexion déjà présent par défaut
//            ->addMenuItems([
//                MenuItem::section(),
//                //MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
//            ])
            ;
    }

//    public function configureCrud(): Crud
//    {
//        return Crud::new()
//            // the first argument is the "template name", which is the same as the
//            // Twig path but without the `@EasyAdmin/` prefix
////            ->overrideTemplates([
////                'crud/field/id' => 'backoffice/field/id.html.twig',
////                'layout' => 'backoffice/layout.html.twig'
////            ]);
//    }
}
