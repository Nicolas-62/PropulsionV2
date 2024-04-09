<?php

namespace App\Controller\Backoffice;

use App\Entity\Category;
use App\Entity\Config;
use App\Entity\Contact;
use Doctrine\DBAL\Types\TextType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ConfigCrudController extends AbstractCrudController
{

    public function __construct
    (
        // Générateur de routes
        protected AdminUrlGenerator $adminUrlGenerator
    )
    {
        // Page de configuration générale
        $this->config_url  = $this->adminUrlGenerator
            ->setController(ConfigCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId(1)
            ->generateUrl();
    }
    public static function getEntityFqcn(): string
    {
        return Config::class;
    }


    public function configureFields(string $pageName): iterable
    {

        // Onglet Configuration générale
        yield FormField::addTab('Configuration générale');
        // Todo : Champs pour lancer le vidage du cache à la sauvegarde des données. A implémenter, désactivé pour l'instant
        //yield IntegerField::new('cache_flush_auto');


        // Onglet Informations Contact
        yield FormField::addPanel('Informations Contact');
        foreach(Contact::SUBJECTS as $subject){
            yield TextField::new('email_'.$subject['variable'],'Email '.$subject['label'])->setColumns(6);
            yield TextField::new('email_'.$subject['variable'].'_object', 'Titre '.$subject['label'])->setColumns(6);
        }
        yield FormField::addPanel('SEO du site');
        yield TextField::new('seo_title',"Titre du site")->setColumns(6);
        yield TextField::new('seo_description',"Description du site")->setColumns(6);
        yield CollectionField::new('seo_keywords', 'Mots clés du site')->setColumns(6);
    }

    /**
     * Redirige vers la page de configuration générale.
     *
     * @param AdminContext $context
     * @return RedirectResponse
     */
    public function index(AdminContext $context)
    {
        // Redirection
        return $this->redirect($this->config_url);
    }

    /**
     * Redirige vers la page de configuration générale.
     *
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|Response
     */
    public function new(AdminContext $context)
    {
        // Redirection
        return $this->redirect($this->config_url);
    }

    /**
     * Redireige vers le detail de la configuration générale, empèche la redirection vers la liste des configurations.
     *
     * @param AdminContext $context
     * @param string $action
     * @return RedirectResponse
     */
    public function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        // Redirection
        return $this->redirect($this->config_url);

    }

}
