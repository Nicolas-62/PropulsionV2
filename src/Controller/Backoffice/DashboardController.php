<?php

namespace App\Controller\Backoffice;

use App\Controller\UserController;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Config;
use App\Entity\Media;
use App\Entity\Mediaspec;
use App\Entity\Theme;
use App\Entity\User;
use App\Notification\BoNotification;
use App\Service\CacheService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Cache\CacheInterface;

#[Route('/backoffice', name: 'bo_')]
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
            return $this->redirectToRoute('login');
        }
        // Controleur par défaut, liste des catégories.
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // Si l'utilisateur est admin, on redirige vers la liste des catégories.
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($adminUrlGenerator->setController(CategoryCrudController::class)->generateUrl());
        }
        // Si l'utilisateur est photographe, on redirige vers la galerie.
        if ($this->isGranted('ROLE_PHOTOGRAPH')) {
            return $this->redirect($adminUrlGenerator->setController(GalleryCrudController::class)->generateUrl());
        }
        return $this->redirect($adminUrlGenerator->setController(ArticleCrudController::class)->generateUrl());

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



    public function clearDir($dossier, $rmdir = false)
    {
        // Ouverture du dossier.
        $ouverture = @opendir($dossier);
        // Si l'ouverture à échouée, on sort de la fonction.
        if (!$ouverture) return;
        // On parcours tout les éléments du dossier.
        while ($fichier = readdir($ouverture)) {
            // Si c'est un fichier système, on le passe.
            if ($fichier == '.' || $fichier == '..') continue;
            // Si c'est un sous-dossier.
            if (is_dir($dossier . "/" . $fichier)) {
                // On le supprime de manière récursive.
                $r = clearDir($dossier . "/" . $fichier);
                // Si la suppréssion à échoué on retourne false.
                if (!$r) return false;
            } else {  // Sinon si c'est un fichier.
                // On le supprime.
                $r = @unlink($dossier . "/" . $fichier);
                // Si la suppréssion à échoué on retourne false.
                if (!$r) return false;
            }
        }
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
            // Langues supportées.
            ->setLocales(
                [
                    'en' => '🇬🇧 English', // locale without custom options
                    'fr' => '🇫🇷 Français',
                ]
            )
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

        // Lien vers le frontoffice
        yield MenuItem::linkToRoute('Aller sur le site', 'fas fa-undo', 'fo_home_index');

        // yield MenuItem::linkToDashboard('dashboard', 'fa fa-home');
        //yield MenuItem::section('Contenu','fa-solid fa-folder');
        if ($this->isGranted('ROLE_ADMIN')) {

            // Liste des Catégories.
            yield MenuItem::linkToCrud('Categories', 'fa-solid fa-bars', Category::class);
            // Liste des Articles.
            yield MenuItem::linkToCrud('Articles', 'fas fa-newspaper', Article::class)->setController(ArticleCrudController::class);
        }
        if ($this->isGranted('ROLE_PHOTOGRAPH')) {

            // Liste des articles de la catégorie galerie.
            yield MenuItem::linkToCrud('Galerie', 'fa-regular fa-images', Article::class)->setController(GalleryCrudController::class);
        }
        if ($this->isGranted('ROLE_ADMIN')) {

            // Liste des images.
            yield MenuItem::linkToCrud('Images', 'fa-regular fa-image', Media::class)->setController(PictureCrudController::class);
            // Liste des fichiers.
            yield MenuItem::linkToCrud('Fichiers', 'fa-regular fa-file', Media::class)->setController(FileCrudController::class);
            // Liste des thèmes.
            yield MenuItem::linkToCrud('Thèmes', 'fa-regular fa-image', Theme::class);

            // Config.
            yield MenuItem::linkToCrud('Configuration', 'fas fa-gear', Config::class)->setAction(Crud::PAGE_EDIT)->setEntityId(1);

            if ($this->isGranted('ROLE_DEV')) {
                // Liste des médiaspecs.
                yield MenuItem::linkToCrud('Médiaspecs', 'fa-regular fa-image', Mediaspec::class);
            }
            // Liste des utilisateurs.
            yield MenuItem::linkToCrud('Uilisateurs', 'fa-solid fa-user', User::class);
            yield MenuItem::linkToRoute('Preview', 'fa-solid fa-eye', 'bo_toggle_preview')->setBadge($this->session->get('preview') ? "ON" : "OFF");
            yield MenuItem::linkToRoute('Vider le Cache', 'fa-solid fa-trash', 'bo_clear_cache');

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

    public function configureCrud(): Crud
    {
        return Crud::new()
            // the first argument is the "template name", which is the same as the
            // Twig path but without the `@EasyAdmin/` prefix
            ->overrideTemplates([
                'crud/field/id' => 'backoffice/field/id.html.twig',
                'layout' => 'backoffice/layout.html.twig'
            ]);
    }
}
