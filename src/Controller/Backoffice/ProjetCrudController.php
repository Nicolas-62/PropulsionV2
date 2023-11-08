<?php

namespace App\Controller\Backoffice;

use App\Entity\Projet;
use App\Field\EtapeField;
use App\Field\ImageUploadField;
use App\Field\MontantHTField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ProjetCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Projet::class;
    }

    public function __construct(
        // Générateur de routes
        protected AdminUrlGenerator $adminUrlGenerator,
    )
    {
        // Nombre d'étapes des projets
        $this->nbEtapes = [1,2,3];
    }

    /**
     * configureCrud permet de configurer le crud, champs de recherche, redirection vers un template spécial, triage ...
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // ...
            ->setEntityLabelInPlural('Projets')
            ->overrideTemplate('crud/index', $this->list_template_name)
            ->showEntityActionsInlined()
            ;

    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // Si l'article n'a pas de slug défini, on ne le sauvegarde pas.
        if($entityInstance->hasError()){
            return;
        }else {
            parent::persistEntity($entityManager, $entityInstance);
        }
    }

    /**
     * getRedirectResponseAfterSave Permet de gérer le comportement apres avoir édité, ajouté ou supprimé une entité
     * @param AdminContext $context
     * @param string $action
     * @return RedirectResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        // Redirige sur la page d'édit si l'entité a une erreur
        if ($context->getEntity()->getInstance()->hasError()) {
            $url = $this->container->get(AdminUrlGenerator::class)
                ->setAction(Action::EDIT)
                ->setEntityId($context->getEntity()->getPrimaryKeyValue())
                ->generateUrl();
            return $this->redirect($url);
        }


        return parent::getRedirectResponseAfterSave($context, $action);
    }


    public function getEtapeFields(){
        // Liste des champs à retourner.
        $etapeFields = array();
        // Pour chaque étape
        foreach($this->nbEtapes as $nbEtape){
            // Ajout du champ
            $etapeFields[] = EtapeField::new('etape'.$nbEtape, 'Etape '.$nbEtape);
        }
        return $etapeFields;
    }

    /**
     * configureResponseParameters Permet d'envoyer des données à la vue.
     * @param KeyValueStore $responseParameters
     * @return KeyValueStore
     */
    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        $twig = $this->container->get('twig');
        $twig->addGlobal('user', $this->getUser());

        return parent::configureResponseParameters($responseParameters);
    }

    /**
     * Définie les assets nécessaires pour le controleur.
     * @param Assets $assets
     * @return Assets
     */
    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addWebpackEncoreEntry(Asset::new('bo_projets'));
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->disable( Action::DELETE);
        return parent::configureActions($actions);
    }

}
