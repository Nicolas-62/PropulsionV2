<?php

namespace App\Controller\Backoffice;

use App\Entity\Category;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;


class CategoryCrudController extends AbstractCrudController
{
    private AdminUrlGenerator $adminUrlGenerator;
    private EntityManagerInterface $entityManager;
    private EntityRepository $entityRepository;

    private ?Category $entity = null;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, EntityRepository $entityRepository, EntityManagerInterface $entityManager)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->entityRepository  = $entityRepository;
        $this->entityManager  = $entityManager;
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
        $category->setPosition($this->entityManager->getRepository(Category::class)->count([]) + 1 );

        return $category;
    }

    /**
     * configureFields permet la configuration des différents champs que l'on va retrouver sur les pages du crud
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            // Champs de la vue liste
            IntegerField::new('position', 'position')->setColumns(6)->hideOnForm(),
            TextField::new('title', 'title')->setColumns(6),
            DateField::new('created_at', 'creation')->hideOnForm(),
            DateField::new('updated_at', 'dernière édition')->hideOnForm(),

            // Champs du formulaire
            AssociationField::new('parent','Parent')->hideOnIndex(),
            BooleanField::new('can_create','can_create')->hideOnIndex()->setColumns(3),
            BooleanField::new('has_multi','has_multi')->hideOnIndex()->setColumns(3),
            BooleanField::new('has_title','has_title')->hideOnIndex()->setColumns(3),
            BooleanField::new('has_sub_title','has_sub_title')->hideOnIndex()->setColumns(3),
            BooleanField::new('has_seo','has_seo')->hideOnIndex()->setColumns(3),
            BooleanField::new('has_link','has_link')->hideOnIndex()->setColumns(3),
            BooleanField::new('has_theme','has_theme')->hideOnIndex()->setColumns(3),
            BooleanField::new('has_content','has_content')->hideOnIndex()->setColumns(3),
            AssociationField::new('children','Enfants'),
            // Champs communs

            // CollectionField::new('grandParent','Grand Parent')->hideOnIndex()->hideOnForm(),

        ];
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
     * index
     *
     * Affiche la liste des categories, affiche les categories enfant si l'id d'un parent est passé en paramètre
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(AdminContext $context)
    {

        // Récupération de l'id de la categorie parent.
        $entityId = $this->adminUrlGenerator->get('entityId');
        // Si on doit afficher les enfants d'une catégorie
        if($entityId != null) {
            // Récupère la categorie pour filtrer dans la requète (voir fonction createIndexQueryBuilder)
            $this->entity = $this->entityManager->getRepository(Category::class)->find($entityId);
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
        // DEBUG
        //dump($searchDto);

        // Récupération du query builder
        $response = $this->entityRepository->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        // Si pas d'ordre
        if($searchDto->getSort() == []) {
            // Ordonne par position
            $response->orderBy('entity.position');
        }
        // Si pas de recherche
        if($searchDto->getQuery() == '') {
            // Si une categorie a été précisée, on veut afficher uniquement ses enfants
            if ($this->entity != null) {
                $response->where('entity.category_id = :entityId');
                $response->setParameter('entityId', $this->entity->getId());
                // Sinon on affiche que les categories qui n'ont pas de parent.
            } else {
                $response->andwhere('entity.category_id IS NULL');
            }
        }
        // Retour
        return $response;
    }

    /**
     * @param Filters $filters
     * @return Filters
     */
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('category_id')
            ;
    }

    /**
     * configureResponseParameters Permet d'envoyer des données à la vue.
     * @param KeyValueStore $responseParameters
     * @return KeyValueStore
     */
    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        // Envoi de l'id du parent à la vue.
        $parentId = $this->entity?->getParentId();
        $responseParameters->set('parentId', $parentId);

        return parent::configureResponseParameters($responseParameters);
    }



    // EXEMPLE...

    /**
     * getRedirectResponseAfterSave permet de gérer le comportement apres avoir éditer , ajouter ou supprimer une entity
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
        $returnAction = Action::new('backToParent', 'Revenir', 'fa fa-arrow-left');
        $returnAction->setTemplatePath('backoffice/actions/back_to_parent.html.twig');
        // renders the action as a <a> HTML element
        $returnAction->displayAsLink();
        $returnAction->linkToCrudAction('index');
        $returnAction->createAsGlobalAction();
        $returnAction->addCssClass('btn btn-primary');

        // Ajout des boutons à la liste des actions disponibles.
        $actions->add(Crud::PAGE_INDEX, $returnAction);

        return $actions;
    }
}
