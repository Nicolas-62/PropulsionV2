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
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;


class CategoryCrudController extends AbstractCrudController
{
    private AdminUrlGenerator $adminUrlGenerator;
    private EntityManagerInterface $entityManager;
    private EntityRepository $entityRepository;

    private int $entityId;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, EntityRepository $entityRepository, EntityManagerInterface $entityManager)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->entityRepository  = $entityRepository;
        $this->entityManager  = $entityManager;
        $this->entityId          = 0;
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
            TextField::new('title', 'title')->setColumns(6),
            IntegerField::new('position', 'position')->setColumns(6),
            DateField::new('created_at', 'creation')->hideOnForm(),
            DateField::new('updated_at', 'update')->hideOnForm(),
            IdField::new('category_id', 'Parent ID')->hideOnDetail()->hideOnIndex(),
            BooleanField::new('can_create','can_create')->hideOnIndex()->hideOnDetail()->setColumns(3),
            BooleanField::new('has_multi','has_multi')->hideOnIndex()->hideOnDetail()->setColumns(3),
            BooleanField::new('has_title','has_title')->hideOnIndex()->hideOnDetail()->setColumns(3),
            BooleanField::new('has_sub_title','has_sub_title')->hideOnIndex()->hideOnDetail()->setColumns(3),
            BooleanField::new('has_seo','has_seo')->hideOnIndex()->hideOnDetail()->setColumns(3),
            BooleanField::new('has_link','has_link')->hideOnIndex()->hideOnDetail()->setColumns(3),
            BooleanField::new('has_theme','has_theme')->hideOnIndex()->hideOnDetail()->setColumns(3),
            BooleanField::new('has_content','has_content')->hideOnIndex()->hideOnDetail()->setColumns(3),
            CollectionField::new('children','Nombre Enfants'),
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

            ;

    }


    /**
     * index renvois vers la page de base de la catégorien souvent lié à la liste
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(AdminContext $context)
    {
        //dump($context);
        return parent::index($context); // TODO: Change the autogenerated stub
    }

    /**
     * detail renvois vers une page détaillant un des objet mis en bdd
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function detail(AdminContext $context)
    {

        //dump($context->getRequest()->get('entityId'));
        //exit();
        $this->entityId = $context->getRequest()->get('entityId');


        //dump($context);

        $url = $this->adminUrlGenerator
            ->setController(CategoryCrudController::class)
            ->setAction('index')
            ->generateUrl();

        //return $this->redirect($url);

        return parent::index($context);
    }



    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $response = $this->entityRepository->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        if($this->entityId != 0) {
            $response->where('entity.category_id = :entity_id');
            $response->setParameter('entity_id', $this->entityId);
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


        $categories = $this->entityManager->getRepository(Category::class)->findAll();
        $responseParameters->set('categories',$categories);
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

    public function configureActions(Actions $actions): Actions
    {
        return $actions;
    }
}
