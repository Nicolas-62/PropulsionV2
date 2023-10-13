<?php

namespace App\Controller\Backoffice;


use App\Entity\Article;
use App\Entity\MediaType;
use App\Field\ImageUploadField;

use App\Service\MediaService;
use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
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
use Symfony\Component\Mime\MimeTypes;
use Twig\Environment;
use Vich\UploaderBundle\Form\Type\VichImageType;
use function PHPUnit\Framework\throwException;


class MediaCrudController extends BoController
{
    // Nom de la section
    public string $section            =   'phototheque';
    // Nom du type de média, utilisé pour nommage des templates
    public string $bo_model_name      =   '';
    public string $bo_models_name     =   '';
    // Nom du type de média dans l'interface
    public string $model_label        =   '';
    public string $models_label       =   '';
    // Nom du type de fichier associé au média (pdf, image, video, ...)
    public string $model_type_label   =   '';
    // Extensions acceptées
    public array $acceptedExtensions  =   [];

    // Intilisés dans le constructeur:

    // Type de fichier accepté par le média
    public MediaType $mediaType;
    // Types de fichiers acceptés
    public array $acceptedFileTypes   =   [];

    public function __construct(
        // Repository EasyAdmin
        protected EntityRepository $entityRepository,
        protected Environment $twig,
        protected EntityManagerInterface $entityManager
    )
    {
        // Récupération des types mime en fonction des extensions acceptées.
        $mimeTypes = new MimeTypes();
        foreach($this->acceptedExtensions as $extension){
            foreach($mimeTypes->getMimeTypes($extension) as $mimetype){
                $this->acceptedFileTypes[] = $mimetype;
            }
        }
        // Récupération du type de média
        $this->mediaType = $this->entityManager->getRepository(MediaType::class)->findOneBy(['label' => $this->model_type_label]);

        // Appel du constructeur du controller parent
        parent::__construct();
    }

    /**
     * Retourne le nom de l'entité gérée par ce controleur.
     *
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    /**
     * Fourni à la vue les variables dont elle a besoin pour fonctionner.
     *
     * @param KeyValueStore $responseParameters
     * @return KeyValueStore
     */
    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        // Si édite un article
        if (Crud::PAGE_EDIT === $responseParameters->get('pageName')
            || Crud::PAGE_NEW === $responseParameters->get('pageName')
        ) {
            // Passage des types de fichiers acceptés
            // !! Passage en global pour que le champ de formulaire puisse y accéder !!
            $this->twig->addGlobal('acceptedFileTypes', $this->acceptedFileTypes);
            $this->twig->addGlobal('acceptedExtensions', $this->acceptedExtensions);

        }

        return parent::configureResponseParameters($responseParameters);
    }


    /**
     * Renvoi vers le formulaire d'édition du media
     *
     * @param AdminContext $context
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
        $media = new $entityFqcn();
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
            ->setEntityLabelInPlural(ucfirst($this->models_label))
            ->setEntityLabelInSingular(ucfirst($this->model_label))
            // Titre de la page et nom de la liste affichée
            ->setHelp('index', 'Liste des '.$this->models_label)
            // Template personnalisé
            ->overrideTemplate('crud/index', 'backoffice/'.$this->bo_model_name.'/'.$this->bo_models_name.'.html.twig')
            // Pagination
            ->setPaginatorPageSize(12)
            ->setPaginatorRangeSize(4)
            // Personnalisation du formulaire
            ->setFormThemes(['backoffice/'.$this->bo_model_name.'/'.$this->bo_model_name.'_edit.html.twig', '@EasyAdmin/crud/form_theme.html.twig'])
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
            return $action->setTemplatePath('backoffice/'.$this->bo_model_name.'/delete_action.html.twig');
        });

        return $actions;
    }

    /**
     * Définie les assets nécessaires pour le controleur
     * @param Assets $assets
     * @return Assets
     */
    public function configureAssets(Assets $assets): Assets
    {
        $assets->addWebpackEncoreEntry('bo_medias');
        return $assets;
    }

    /**
     * Requêtage des entités à afficher.
     *
     * @param SearchDto $searchDto
     * @param EntityDto $entityDto
     * @param FieldCollection $fields
     * @param FilterCollection $filters
     * @return QueryBuilder
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        // récupération des articles.
        $response = $this->entityRepository->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        // On filtre par la section
        $response->andWhere('entity.section = :section');
        $response->setParameter('section', $this->section);

        // On filtre les médias par type
        $response->join('entity.mediaType', 'mediaType');
        $response->andWhere('mediaType.label = :type');
        $response->setParameter('type', $this->model_type_label);

        // Si pas d'ordre
        if($searchDto->getSort() == [])
        {
            // Ordonne par ordre
            $response->orderBy('entity.created_at', 'DESC');
        }
        return $response;
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
        yield TextField::new('legend', 'description')->setEmptyData('');
        // Vignette du média dans la liste des médias.
        yield ImageUploadField::new('thumbnail', $this->model_label)->onlyOnIndex();
        yield DateField::new('created_at','création')->hideOnForm();
        yield DateField::new('updated_at','dernière édition')->hideOnForm();

        // FORMULAIRE
        // Champ personnalisé d'upload du média
        $mediaField = Field::new($this->getModelName(), $this->model_label)
            ->onlyOnForms()
            ->setRequired(true)
            ->setFormTypeOptions([
                'block_name' => $this->bo_model_name.'_edit',
            ])
        ;
        // Si le média existe déjà.
        if(Crud::PAGE_EDIT === $pageName) {
            // On personnalise la vue, on affiche l'image.
            $mediaField->setDisabled();
        }
        yield $mediaField;
    }
}
