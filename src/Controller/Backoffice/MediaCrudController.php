<?php

namespace App\Controller\Backoffice;


use App\Field\MediaUploadField;

use App\Service\MediaService;
use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Vich\UploaderBundle\Form\Type\VichImageType;
use function PHPUnit\Framework\throwException;

/**
 * @method UploadedFile move()
 */
class MediaCrudController extends BoController
{


    public function __construct(
    )
    {
        // Appel du constructeur du controller parent
        parent::__construct();
    }
    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    /**
     * Configure les champs à afficher dans les interfaces.
     *
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm()->setPermission('ROLE_DEV');
        // Nom du média.
        yield TextField::new('getName', 'nom')->onlyOnIndex();
        yield TextField::new('legend', 'description');
// TEST VICHUPLOAD BUNDLE
//        yield ImageField::new('media', 'Image')
//            ->onlyOnIndex()
//            ->setBasePath('/assets/dyn-upload');
//        yield MediaUploadField::new('mediaFile', 'Image')
//            ->onlyOnForms()
//            ->setFormType(VichImageType::class);

        // Vignette du média dans la liste des médias.
        yield MediaUploadField::new('media', 'Image')->onlyOnIndex();

        // FORMULAIRE

        // Champ personnalisé d'upload du média
        $mediaField = Field::new('media', 'Image')
            ->onlyOnForms()
            ->setRequired(true)
            ->setFormTypeOptions([
                'block_name' => 'media_edit',
            ])
        ;

        // Si le média existe déjà.
        if(Crud::PAGE_EDIT === $pageName) {
            // On personnalise la vue, on affiche l'image.
            $mediaField->setDisabled();
        }

        yield $mediaField;

    }


    /**
     * Renvoi vers le formulaire d'édition du media
     *
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(AdminContext $context)
    {
        // Récupération de l'article
        $this->entity = $context->getEntity()->getInstance();
        return parent::edit($context);
    }

    /**
     * Permet de donner des valeurs par défaut aux différents champs de notre entité
     *
     * @param string $entityFqcn
     * @return Media
     */
    public function createEntity(string $entityFqcn)
    {
        $media = new Media();
        $media->setCreatedAt( new \DateTimeImmutable() );
        $media->setUpdatedAt( new \DateTimeImmutable() );

        return $media;
    }

    /**
     * Retourne la liste des entités liées au média passé en paramètre.
     *
     * @param EntityManagerInterface $entityManager
     * @param AdminContext $context
     * @return JsonResponse
     */
    public function getRelatedEntities(EntityManagerInterface $entityManager, AdminContext $context): JsonResponse
    {

        // Récupération du média passé en paremètre.
        $this->entity = $context->getEntity()->getInstance();
        // Récupération des entités liées.
        $entities = $entityManager->getRepository(Media::class)->getRelatedEntities($this->entity);

        // Retour
        return new JsonResponse(["entities" => $entities]);
    }

    /**
     * Permet de configurer le crud, champs de recherche, redirection vers un template spécial, triage ...
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // ...
            ->setEntityLabelInPlural('Medias')
            // Titre de la page et nom de la liste affichée
            ->setHelp('index', 'Liste des médias')
            // Template personnalisé
            ->overrideTemplate('crud/index', 'backoffice/media/medias.html.twig')

            // Pagination
            ->setPaginatorPageSize(12)
            ->setPaginatorRangeSize(4)
            // Personnalisation du formulaire
            ->setFormThemes(['backoffice/media/media_edit.html.twig', '@EasyAdmin/crud/form_theme.html.twig'])
            // Actions sur la liste visible (par défaut cachées dans un dropdown)
            ->showEntityActionsInlined()
            ;
    }

    function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {

        parent::deleteEntity($entityManager, $entityInstance); // TODO: Change the autogenerated stub
    }

    /**
     * Défini les actions suppélemnetaires disponibles dans la vue
     *
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        // Surcharge du bouton de suppression du média, vérification si il est lié à des publications
        $actions->update(Crud::PAGE_INDEX,'delete', function(Action $action){
            return $action->setTemplatePath('backoffice/media/delete_action.html.twig');
        });

        return $actions;
    }


    /**
     * Définie les assets nécessaires pour le controleur de médias.
     * @param Assets $assets
     * @return Assets
     */
    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addWebpackEncoreEntry('bo_medias');
    }
}
