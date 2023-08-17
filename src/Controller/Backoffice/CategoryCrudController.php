<?php

namespace App\Controller\Backoffice;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Language;
use App\Entity\Seo;
use App\Field\LanguageSelectField;
use App\Form\SeoType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;


class CategoryCrudController extends AbstractCrudController
{
    // Variables.


    private ?Category $entity = null;

    public function __construct(
        // Services

        // Générateur de routes
        private AdminUrlGenerator $adminUrlGenerator,
        // Gestionnaire d'entité Symfony
        private EntityManagerInterface $entityManager,
        // Repository EasyAdmin
        private EntityRepository $entityRepository,
        private RequestStack $requestStack,
        // Code Langue
        private string $locale
    )
    {
    }


    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    /**
     * createEntity permet de donner des valeurs par défaut aux différents champs de notre entité
     * @param string $entityFqcn
     * @return Category
     */
    public function createEntity(string $entityFqcn)
    {
        $category = new Category();
        $category->setCreatedAt(new \DateTimeImmutable());
        $category->setUpdatedAt(new \DateTimeImmutable());
        $category->setOrdre($this->entityManager->getRepository(Category::class)->count([]) + 1 );

        return $category;
    }

    /**
     * configureFields permet la configuration des différents champs que l'on va retrouver sur les pages du crud
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {

        // Entité category.
        $model = new Category();

        // Contenu de la catégorie
        yield FormField::addTab('Paramètres');
            // Champs de la vue liste
        yield IdField::new("id")->hideOnForm()->setPermission('ROLE_DEV');
        yield IntegerField::new('ordre', 'ordre')->setColumns(6)->hideOnForm();
        yield TextField::new('title', 'title')->setColumns(6);
        yield DateField::new('created_at', 'creation')->hideOnForm();
        yield DateField::new('updated_at', 'dernière édition')->hideOnForm();
        yield AssociationField::new('children','Categories')->hideOnForm();
        yield AssociationField::new('articles', 'Articles')->hideOnForm();

        // Champs du formulaire
        yield AssociationField::new('parent','Parent')->hideOnIndex()->setRequired(false);
        yield BooleanField::new('can_create','can_create')->hideOnIndex()->setColumns(3);
        yield BooleanField::new('has_multi','has_multi')->hideOnIndex()->setColumns(3);
        yield BooleanField::new('has_title','has_title')->hideOnIndex()->setColumns(3);
        yield BooleanField::new('has_sub_title','has_sub_title')->hideOnIndex()->setColumns(3);
        yield BooleanField::new('has_seo','has_seo')->hideOnIndex()->setColumns(3);
        yield BooleanField::new('has_link','has_link')->hideOnIndex()->setColumns(3);
        yield BooleanField::new('has_theme','has_theme')->hideOnIndex()->setColumns(3);
        yield BooleanField::new('has_content','has_content')->hideOnIndex()->setColumns(3);
        yield BooleanField::new('isOnline')->hideOnForm();

        if($pageName === Crud::PAGE_EDIT) {

            // CONTENU
            // Onglet Contenu, contient les champs extra, éditables en fonction de la langue.
            yield FormField::addTab('Contenu');
            // Récupération des langues
            $languages = $this->entityManager->getRepository(Language::class)->getAllForChoices();
            // Si on a plusieurs langues actives.
            if(count($languages) > 1) {
                // Sélecteur de langue pour édition du contenu en fonction de la langue
                yield LanguageSelectField::new('language', 'langue')->setChoices(
                    $this->entityManager->getRepository(Language::class)->getAllForChoices()
                );
            }
            // SEO
            // Si un de ses parent a de la SEO
            if($this->entityManager->getRepository(Category::class)->hasSeo($this->entity)){
                // Récupération de la seo de la langue courante.
                $seo = $this->entity->getSeo($this->locale);
                // Si la seo n'existe pas on retourne un objet vide.
                if($seo == null){
                    $seo = new Seo();
                }
                // Création d'un champ avec un vue customisée
                yield CollectionField::new('seo','Seo')
                    ->setFormTypeOptions([
                        // Voir template : seo_edit.html.twig
                        'block_name' => 'seo_edit',
                        // Passage de la seo dans les champs du formulaire
                        'data' => ['seo' => $seo]
                    ])
                ;
            }
            // Ajout des champs spécifiques à l'instance définis dans l'entité.
            foreach($model->getExtraFields() as $extraField){
                yield $model->getEasyAdminFieldType($extraField['ea_type'])::new($extraField['name'], $extraField['label'])->setColumns(12);
            }

        }


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
            ->setEntityLabelInPlural('Categories')
            // Titre de la page et nom de la liste affichée
            ->setPageTitle('index', function (){
                if($this->entity != null){
                    return 'Categorie : '.$this->entity->getTitle();
                }
            })
            ->setHelp('index', 'Liste des sous catégories')
            // Template personnalisé
            ->overrideTemplate('crud/index', 'backoffice/category/categories.html.twig')
            // Personnalisation du formulaire
            ->setFormThemes([
                    'backoffice/category/seo_edit.html.twig',
                    '@EasyAdmin/crud/form_theme.html.twig'
                ]
            )
           // Champs de recherche
            //->setSearchFields(['title'])
            // ->setDefaultSort(['id' => 'DESC'])
            // ->setPaginatorPageSize(30)
            // ->setPaginatorRangeSize(4)
            // Actions sur la liste visible (par défaut cachées dans un dropdown)
            ->showEntityActionsInlined()
            ;

    }


    /**
     *
     * Affiche la liste des categories, affiche les categories enfant si l'id d'un parent est passé en paramètre
     *
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(AdminContext $context)
    {
      // Récupération de l'id de la categorie parent.
        $entityId   =    $this->adminUrlGenerator->get('entityId');
        // Si on doit afficher les enfants d'une catégorie
        if($entityId != null) {
            // Récupère la categorie pour filtrer dans la requète (voir fonction createIndexQueryBuilder)
            $this->entity   =   $this->entityManager->getRepository(Category::class)->find($entityId);
        }
        return parent::index($context);
    }

    /**
     * detail
     *
     * Affiche la liste des enfants de la categorie dont l'id est passé en paramètre
     * , redirige vers la méthode : index
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function detail(AdminContext $context)
    {
        // Génération de l'URL, ajout en paramètre de l'id de la categorie.
        $url = $this->adminUrlGenerator->setAction(Action::INDEX)
                ->set('entityId', $context->getEntity()->getInstance()->getId())
            ->generateUrl();

        // Redirection
        return $this->redirect($url);
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
        // Récupération des datas de la publication
        $this->entity->getDatas($this->locale);

        // Si ce n'est pas un sous article, on récupère sa categorie parent
        //$this->category = $this->entity->getParent();

        return parent::edit($context);
    }


    /**
     * createIndexQueryBuilder
     *
     * Execute la requête qui récupère les categories à afficher en vue liste
     *
     * @param SearchDto $searchDto
     * @param EntityDto $entityDto
     * @param FieldCollection $fields
     * @param FilterCollection $filters
     * @return QueryBuilder
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {

        // Récupération du query builder
        $response = $this->entityRepository->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        // Si pas d'ordre
        if($searchDto->getSort() == [])
        {
            // Ordonne par ordre
            $response->orderBy('entity.ordre');
        }
        // Si pas de recherche
        if($searchDto->getQuery() == '')
        {
            // Si une categorie a été précisée, on veut afficher uniquement ses enfants
            if ($this->entity != null)
            {
                $response->where('entity.category_id = :entityId');
                $response->setParameter('entityId', $this->entity->getId());
            }
            // Sinon on affiche que les categories qui n'ont pas de parent.
            else {
                $response->andwhere('entity.category_id IS NULL');
            }
        }
        // Retour
        return $response;
    }

    /**
     * configureFilters : définis les filtres applicables (voir bouton 'Filtres' dans les actions globales)
     * @param Filters $filters
     * @return Filters
     */
    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('category_id');
    }

    /**
     * configureResponseParameters Permet d'envoyer des données à la vue.
     * @param KeyValueStore $responseParameters
     * @return KeyValueStore
     */
    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        // Passage du parent des enfants de la liste affichée.
        $responseParameters->set('parentId', $this->entity?->getId());
        // Envoi de l'id du grand-parent à la vue.
        $ancestorId = $this->entity?->getParent()?->getId();
        $responseParameters->set('ancestorId', $ancestorId);
        $responseParameters->set('crudControllerName', 'Category');
        $responseParameters->set('keyName', 'entityId');

        return parent::configureResponseParameters($responseParameters);
    }

    // FONCTION EXEMPLE, REDIRECTION APRES SAUVEGARDE
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
        $submitButtonName = $context->getRequest()->request->all()['ea']['newForm']['btn'];

        if ('saveAndViewDetail' === $submitButtonName) {
            $url = $this->container->get(AdminUrlGenerator::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($context->getEntity()->getPrimaryKeyValue())
                ->generateUrl();

            return $this->redirect($url);
        }

        return parent::getRedirectResponseAfterSave($context, $action);
    }



    /**
     * configureActions
     *
     * Configure les boutons d'action disponibles dans l'interface.
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        // Bouton de retour au détail du parent.
        $returnAction = Action::new('return', 'Revenir', 'fa fa-arrow-left');
        $returnAction->setTemplatePath('backoffice/actions/return.html.twig');
        // renders the action as a <a> HTML element
        $returnAction->displayAsLink();
        // associé à l'action index
        $returnAction->linkToCrudAction('index');
        // Action globale disponible en haut à droite du tableau.
        $returnAction->createAsGlobalAction();
        $returnAction->addCssClass('btn btn-primary');
        // Ajout des boutons à la liste des actions disponibles.
        $actions->add(Crud::PAGE_INDEX, $returnAction);

        return $actions;
    }
}
