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
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
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
        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        $indice = 0 ;
        foreach ( $categories as $cat){
            $indice = $indice + 1;
        }

        $category = new Category();
        $category->setCreatedAt(new \DateTimeImmutable());
        $category->setUpdatedAt(new \DateTimeImmutable());
        $category->setPosition($indice + 1 );

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
            IntegerField::new('position', 'position')->setColumns(6),
            TextField::new('title', 'title')->setColumns(6),
            DateField::new('created_at', 'creation')->hideOnForm(),
            DateField::new('updated_at', 'dernière édition')->hideOnForm(),
            IdField::new('category_id', 'Parent ID')->hideOnDetail()->hideOnIndex(),
            BooleanField::new('can_create','can_create')->hideOnIndex()->hideOnDetail()->setColumns(3),
            BooleanField::new('has_multi','has_multi')->hideOnIndex()->hideOnDetail()->setColumns(3),
            BooleanField::new('has_title','has_title')->hideOnIndex()->hideOnDetail()->setColumns(3),
            BooleanField::new('has_sub_title','has_sub_title')->hideOnIndex()->hideOnDetail()->setColumns(3),
            BooleanField::new('has_seo','has_seo')->hideOnIndex()->hideOnDetail()->setColumns(3),
            BooleanField::new('has_link','has_link')->hideOnIndex()->hideOnDetail()->setColumns(3),
            BooleanField::new('has_theme','has_theme')->hideOnIndex()->hideOnDetail()->setColumns(3),
            BooleanField::new('has_content','has_content')->hideOnIndex()->hideOnDetail()->setColumns(3),
            CollectionField::new('children','Enfants'),
            CollectionField::new('parent','Parent')->hideOnIndex(),
            CollectionField::new('grandParent','Grand Parent')->hideOnIndex()->hideOnForm(),

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

            ->overrideTemplate('crud/index', 'backoffice/category/categories.html.twig')
            // ->setSearchFields(['name', 'description'])
            // ->setDefaultSort(['id' => 'DESC'])
            // ->setPaginatorPageSize(30)
            // ->setPaginatorRangeSize(4)
            ->showEntityActionsInlined()

            ;

    }


    /**
     * index renvois vers la page de base de la catégorie souvent lié à la liste
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(AdminContext $context)
    {
        $entity_id = $this->adminUrlGenerator->get('entityId');

        dump($context);
        if($entity_id != null) {
            $this->entity = $this->entityManager->getRepository(Category::class)->find($entity_id);
        }
        return parent::index($context);
    }

    /**
     * detail renvois vers une page détaillant un des objets mis en bdd
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function detail(AdminContext $context)
    {
        $url = $this->adminUrlGenerator->setAction(Action::INDEX)
                ->set('entityId', $context->getEntity()->getInstance()->getId())
            ->generateUrl();
        return $this->redirect($url);
        //return parent::index($context);
    }


    /**
     * @param SearchDto $searchDto
     * @param EntityDto $entityDto
     * @param FieldCollection $fields
     * @param FilterCollection $filters
     * @return QueryBuilder
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $response = $this->entityRepository->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $response->orderBy('entity.position');
        if($this->entity != null) {
            $response->where('entity.category_id = :entity_id');
            $response->setParameter('entity_id', $this->entity->getId());
        }else{
            $response->where('entity.category_id IS NULL');
        }

        return $response;
    }

    /**
     * configureResponseParameters Permet d'envoyer des données à la vue.
     * @param KeyValueStore $responseParameters
     * @return KeyValueStore
     */
    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {


//        $categories = $this->entityManager->getRepository(Category::class)->findAll();
//        $responseParameters->set('categories',$categories);
//
//        $category = null;
//        if($this->entity != null) {
//            $category = $this->entityManager->getRepository(Category::class)->find($this->entity);
//        }
//        $responseParameters->set('category',$category);

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
     * Définis la redirection du bouton de retour, si l'entité possède un grand parent, redirige vers le détail du grand parent, sinon vers la liste des categories.
     *
     * @param AdminContext $context
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
//    public function defineReturn(AdminContext $context)
//    {
//        // URL par défaut, liste des catégories.
//        $url = $this->container->get(AdminUrlGenerator::class)
//            ->setAction(Action::INDEX)
//            ->generateUrl();
//
//        $category = $context->getEntity()->getInstance();
//        if($category != null) {
//            $parent= $category->getParent();
//            if($parent != null){
//                $url = $this->container->get(AdminUrlGenerator::class)
//                    ->setAction(Action::DETAIL)
//                    ->setEntityId($parent->getId())
//                    ->generateUrl();
//            }
//        }
//        return $this->redirect($url);
//    }
    public function returnAction(AdminContext $context)
    {
        // URL par défaut, liste des catégories.
        $url = $this->container->get(AdminUrlGenerator::class)
            ->setAction(Action::INDEX);
        $entity_id     = $context->getRequest()->query->get('entityId');
        $category = $this->entityManager->getRepository(Category::class)->find($entity_id);

        if($category != null) {
            dump($category->getId());
            $parent= $category->getParent();
            dump($parent->getId());
            if($parent->getId() != null){
                $url->set('entityId', $context->getEntity()->getInstance()->getId());
            }
        }
        $url->generateUrl();
        return $this->redirect($url);
    }
    public function configureActions(Actions $actions): Actions
    {
        $returnAction = Action::new('returnAction', 'Revenir', 'fa fa-arrow-left');
        // renders the action as a <a> HTML element
        $returnAction->displayAsLink();
        $returnAction->linkToCrudAction('returnAction');
        $returnAction->createAsGlobalAction();
        $returnAction->addCssClass('btn btn-primary');

        $actions->add(Crud::PAGE_INDEX, $returnAction);

        return $actions;
    }
}
