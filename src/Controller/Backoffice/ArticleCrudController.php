<?php

namespace App\Controller\Backoffice;

use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\ArticleData;
use App\Entity\Category;
use App\Entity\Media;
use App\Entity\MediaLink;
use App\Entity\Traits\ExtraDataTrait;
use App\Field\ExtraField;
use App\Field\MediaSelectField;
use App\Field\MediaUploadField;
use App\Service\MediaService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField as BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class ArticleCrudController extends BoController
{
    // Variables

    // Article courant
    private ?Article $entity = null;
    // Categorie parent.
    private ?Category $category = null;
    // Code langue courant.
    private string $locale = '';

    public function __construct(
        // Services

        // Générateur de routes
        private AdminUrlGenerator $adminUrlGenerator,
        // Gestionnaire d'entité Symfony
        private EntityManagerInterface $entityManager,
        // Repository EasyAdmin
        private EntityRepository $entityRepository,
    )
    {
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
        return Article::class;
    }

    /**
     * Configure les filtres disponibles.
     *
     * @param Filters $filters
     * @return Filters
     */
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('article_id')
            ->add('category')
            ;
    }

    /**
     * Permet la configuration des différents champs que l'on va retrouver sur les pages du crud
     *
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        // Onglet
        yield FormField::addTab('Contenu');
        // Champs communs à plusieurs actions (liste, edition, detail, formulaire...)
        yield IdField::new('id')->hideOnForm();
        yield IntegerField::new('ordre', 'ordre')->hideOnForm();
        yield DateField::new('created_at','création')->hideOnForm();
        yield DateField::new('updated_at','dernière édition')->hideOnForm();
        yield AssociationField::new('children','Enfants')->hideOnForm();
        yield BooleanField::new('isOnline', 'En ligne')->hideOnForm();
        yield TextField::new('title','titre')->setColumns(6);
        yield TextEditorField::new('content','contenu')->setColumns(12);
        
        $article = new Article();
        // Ajout des champs spécifiques à l'instance définis dans l'entité.
        foreach($article->getExtraFields() as $extraField){
            // Récupération du type easy admin du champ
            yield $article->getEasyAdminFieldType($extraField['ea_type'])::new($extraField['name'], $extraField['label'])
//                ->formatValue(function ($value, $entity) {
//                    return (string) $value;
//                })
                ->setColumns(12);
        }

        // Champs pour l'édition d'un article.
        if(in_array($pageName, [Crud::PAGE_EDIT, Crud::PAGE_NEW])) {
            // Article parent
            yield AssociationField::new('parent', 'Article Parent')->hideOnDetail()->setColumns(6)->hideOnIndex()->setRequired(false);
            // Catégorie parent
            yield AssociationField::new('category', 'Catégorie Parent')->hideOnDetail()->setColumns(6)->hideOnIndex()->setRequired(false);
            // En édition on peut ajouter/enlever des médias.
            if($pageName === Crud::PAGE_EDIT) {
                // Médiaspecs appliquées à l'entité
                $mediaspecs = $this->entityManager->getRepository(Article::class)->getMediaspecs($this->entity);
                // Si ils existent.
                if ($mediaspecs != null) {
                    // Ajout d'un onglet
                    yield FormField::addTab('Medias')
                        ->setIcon('image');
                    // Pour chaque mediaspec
                    foreach ($mediaspecs as $index => $mediaspec) {
                        yield FormField::addRow();

                        // Ajout d'un champ d'upload d'un média
                        // Ajout du personnalisé  champ média.
                        yield $imageField = Field::new('media' . ($index + 1), ucfirst($mediaspec->getName()) . ' : téléchargez un média ou...');
                        $imageField->setColumns(6);
                        // Récupération du média.
                        $media = $this->entityManager->getRepository(Article::class)->getMedia($this->entity, $mediaspec);
                        // Si l'entité possède un média pour cette mediaspec.
                        if ($media != null) {
                            $imageField->setLabel(ucfirst($mediaspec->getName()))
                                // On associe le média existant au champ configuré
                                ->setValue($media)
                                // On définit la vue dédiée à l'affichage du média
                                ->setFormTypeOptions([
                                    'block_name' => 'media_delete',
                                ])
                            ;
                        }
                        // Si pas encore de média défini.
                        else {
                            // Ajout d'une zone d'upload de fichier
                            $imageField
                                ->setFormTypeOptions([
                                'block_name' => 'media_edit',
                            ])
                            ;
                            // Ajout d'un champ supplémentaire de sélection d'un média existant.
                            yield MediaSelectField::new('media' . ($index + 11))
                                ->setChoices(
                                    $this->entityManager->getRepository(Media::class)->getAllForChoices()
                                )
                            ;

                        }
                    }
                }
            }
        }
    }
//      by_reference
//    Similarly, if you're using the CollectionType field where your underlying collection data is an object (like with Doctrine's ArrayCollection),
//    then by_reference must be set to false if you need the adder and remover (e.g. addAuthor() and removeAuthor()) to be called.
//    https://symfony.com/doc/current/reference/forms/types/collection.html#by-reference


    /**
     * Permet de definir les valeurs par défaut de l'entité
     * 
     * @param string $entityFqcn
     * @return Article
     */
    public function createEntity(string $entityFqcn)
    {

        $article = new Article();
        $article->setCreatedAt( new DateTimeImmutable() );
        $article->setUpdatedAt( new DateTimeImmutable() );
        $article->setOrdre( $this->entityManager->getRepository(Article::class)->count([]) + 1 );
//        if($entityId != null){
//            $article->setArticleId($entityId);
//        }
//
        return $article;
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
        // Si on affiche les articles d'une catégorie
        if($this->category != null)
        {
            $response->where('entity.category = :element');
            $response->setParameter('element', $this->category->getId());
        }
        // Si on affiche les sous articles d'un article
        else if($this->entity != null)
        {
            $response->andWhere('entity.article_id = :entity_id');
            $response->setParameter('entity_id', $this->entity->getId());

        }
        // Sinon on affiche tous les articles qui ne sont pas des sous articles.
        else
        {
            $response->andWhere('entity.article_id IS NULL');
        }
        // Tri par ordre.
        $response->orderBy('entity.ordre');
        return $response;
    }


    /**
     * Affiche la liste des enfants ou la liste des parents.
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|Response
     */
    public function index(AdminContext $context)
    {
        // Liste des articles d'une catégorie
        $categoryId = $this->adminUrlGenerator->get('categoryId');
        if($categoryId != null)
        {
            // Récupère la categorie pour filtrer dans la requête
            $this->category = $this->entityManager->getRepository(Category::class)->find($categoryId);
        }
        // Liste des sous articles d'un article
        $entityId = $this->adminUrlGenerator->get('entityId');
        if($entityId != null)
        {
            // Récupère l'article pour filtrer dans la requète (voir fonction createIndexQueryBuilder)
            $this->entity = $this->entityManager->getRepository(Article::class)->find($entityId);
        }
        return parent::index($context);
    }

    /**
     * Renvoi vers une page détaillant un des objets mis en bdd
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|Response
     */
    public function detail(AdminContext $context)
    {
        // Récupération de l'article
        $entity = $context->getEntity()->getInstance();
        // Si il n'a pas d'enfants, on affiche le détail de l'article
        if($entity->getChildren() == null)
        {
            return parent::detail($context);
        }
        // Sinon on affiche la liste de ses enfants.
        else
        {
            // Génération de l'URL, ajout en paramètre de l'id de l'article
            $url = $this->adminUrlGenerator->setAction(Action::INDEX)
                ->set('entityId', $entity->getId())
                ->generateUrl();
            // Redirection
            return $this->redirect($url);
        }
    }

    /**
     * Renvoi vers le formulaire d'édition de l'article
     *
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|Response
     */
    public function edit(AdminContext $context)
    {
        // Récupération de l'article
        $this->entity = $context->getEntity()->getInstance();
        // Récupération des datas de l'article
        $this->entity->getDatas($this->locale);

        // Si ce n'est pas un sous article, on récupère sa categorie parent
        if($this->entity->getParent() == null)
        {
            $this->category = $this->entity->getCategory();
        }
        return parent::edit($context);
    }

    /**
     * supprime l'identifiant de la publication dans l'url pour rediriger vers une liste d'articles.
     *
     * @param AdminContext $context
     * @param string $action
     * @return RedirectResponse
     */
    public function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        // Génération de l'URL
        $url = $this->adminUrlGenerator->setAction(Action::INDEX)
            ->unset('entityId')
            ->generateUrl();
        // Redirection
        return $this->redirect($url);
    }

    /**
     * Permet de configurer le crud, champs de recherche, redirection vers un template spécial, triage ...
     *
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        // Récupération de la langue courante.
        $this->locale            = $this->getParameter('locale');
        // Parametrage du crud.
        return $crud
            // ...
            ->setEntityLabelInPlural('Articles')
            // Titre de la page et nom de la liste affichée
            ->setPageTitle('index', function (){
                if($this->entity != null){
                    return 'Article : '.$this->entity->getTitle();
                }else if($this->category != null){
                    return 'Articles de la catégorie : '.$this->category->getTitle();
                }
            })
            ->setPaginatorPageSize(12)
            ->setPaginatorRangeSize(4)
            // Surcharge des templates de base.
            ->overrideTemplate('crud/index', 'backoffice/article/articles.html.twig')
            ->overrideTemplate('crud/detail', 'backoffice/article/article.html.twig')
            ->overrideTemplate('crud/edit', 'backoffice/article/edit.html.twig')
            // Personnalisation du formulaire
            ->setFormThemes(['backoffice/article/media_edit.html.twig', 'backoffice/article/media_delete.html.twig','backoffice/article/media_select.html.twig','@EasyAdmin/crud/form_theme.html.twig'])

            //showEntityActionsInlined : permet d'afficher les actions en ligne plutôt que dans un menu
            ->showEntityActionsInlined()
            // Help : met une icône ? à côté du titre avec un text quand on passe la souris dessus
            ->setHelp('detail',"Message d'aide");
    }

    /**
     * Fourni à la vue les variables dont elle a besoin pour fonctionner.
     *
     * @param KeyValueStore $responseParameters
     * @return KeyValueStore
     */
    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        // Identifiant du parent (catégorie ou article)
        $parentId               =   null;
        // Identifiant du parent depuis lequel on affichera la liste de ses enfants en cas d'action retour.
        // Ex : depuis la liste d'articles d'une catégorie, le retour se fera sur la liste des catégories du parent de celle ci (soit le grand parent de l'article).
        // Ex : depuis le détail d'un article, le retour se fera sur la liste des articles de son parent (catégorie ou article).
        $ancestorId             =   null;
        // Variables passées pour la navigation dans la vue.

        // Nom du controleur parent dans l'arboresence.
        $crudControllerName     =   'Category';
        // nom du champ du modèle parent sur lequel filtrer.
        $keyName                =   'entityId';

        // Si on affiche une liste d'article
        if (Crud::PAGE_INDEX === $responseParameters->get('pageName')) {
            // Si on est sur la liste des articles d'une catégorie.
            if ($this->category != null) {
                $parentId         =   $this->category->getId();
                // Envoi de l'id du grand-parent à la vue.
                $ancestorId  =   $this->category->getParent()?->getId();
            }
            // Si on est sur la liste des sous articles d'un article.
            else if ($this->entity != null) {
                $parentId         =   $this->entity->getId();
                // Envoi de l'id du grand-parent à la vue.
                $ancestorId  =   $this->entity->getCategory()?->getId();
                $crudControllerName =   'Article';
                $keyName        =   'categoryId';
            }
        }
        // Si on affiche le détail d'un article
        else if (Crud::PAGE_EDIT === $responseParameters->get('pageName')) {
            $crudControllerName     =   'Article';
            // Si on est sur la liste des articles d'une catégorie.
            if ($this->category != null)
            {
                // Envoi de l'id du parent à la vue.
                $ancestorId  =   $this->category->getId();
                $keyName        =   'categoryId';
            } // Si on est sur la liste des sous articles d'un article.
            else if ($this->entity != null)
            {
                // Envoi de l'id du parent à la vue.
                $ancestorId  =   $this->entity->getParent()?->getId();
            }
            // Envoi des mediaspecs à la vue
            $responseParameters->set('mediaspecs', $this->entityManager->getRepository(Article::class)->getMediaspecs($this->entity));

        }
        // Passage des variables dans la vue
        $responseParameters->set('parentId', $parentId);
        $responseParameters->set('crudControllerName', $crudControllerName);
        $responseParameters->set('ancestorId', $ancestorId);
        $responseParameters->set('keyName', $keyName);

        return parent::configureResponseParameters($responseParameters);
    }

    /**
     * Défini les actions suppélemnetaires disponibles dans la vue
     *
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {

        // Bouton de retour au détail du parent.
        $returnGlobalAction = Action::new('return', 'Revenir', 'fa fa-arrow-left');
        $returnGlobalAction->setTemplatePath('backoffice/actions/return.html.twig');
        // renders the action as a <a> HTML element
        $returnGlobalAction->displayAsLink();
        // associé à l'action index
        $returnGlobalAction->linkToCrudAction('index');
        // Action globale disponible en haut à droite du tableau.
        $returnGlobalAction->createAsGlobalAction();
        $returnGlobalAction->addCssClass('btn btn-primary');
        // Ajout des boutons à la liste des actions disponibles.
        $actions->add(Crud::PAGE_INDEX, $returnGlobalAction);


        // Bouton de retour au détail du parent.
        $returnPageAction = Action::new('return', 'Revenir', 'fa fa-arrow-left');
        $returnPageAction->setTemplatePath('backoffice/actions/return.html.twig');
        // renders the action as a <a> HTML element
        $returnPageAction->displayAsLink();
        // associé à l'action index
        $returnPageAction->linkToCrudAction('index');
        $returnPageAction->addCssClass('btn btn-primary');
        // Ajout des boutons à la liste des actions disponibles.
        $actions->add(Crud::PAGE_EDIT, $returnPageAction);

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
            ->addWebpackEncoreEntry(Asset::new('bo_article')->ignoreOnIndex())
            ->addWebpackEncoreEntry(Asset::new('bo_articles')->onlyOnIndex());
    }

}
