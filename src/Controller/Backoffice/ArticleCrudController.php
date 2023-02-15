<?php

namespace App\Controller\Backoffice;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
class ArticleCrudController extends AbstractCrudController
{

    private AdminUrlGenerator $adminUrlGenerator;
    private EntityManagerInterface $entityManager;
    private EntityRepository $entityRepository;
    private int $entityId;
    private ?Article $parentEntity = null;
    private ?Article $entity = null;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, EntityRepository $entityRepository, EntityManagerInterface $entityManager)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->entityRepository  = $entityRepository;
        $this->entityManager     = $entityManager;
        $this->entityId          = 0;



    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    /**
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
     * configureFields permet la configuration des différents champs que l'on va retrouver sur les pages du crud
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            IntegerField::new('position', 'position')->hideOnForm(),
            TextField::new('title','titre')->setColumns(6),
            TextEditorField::new('content','description')->setColumns(12),
            DateField::new('created_at','créé à')->hideOnForm(),
            DateField::new('updated_at','dernière édition')->hideOnForm(),
            AssociationField::new('children','Enfants')->hideOnForm(),
            AssociationField::new('parent','Article Parent')->hideOnDetail()->setColumns(6)->hideOnIndex(),
            AssociationField::new('category','Categorie parent')->setColumns(6),
            IntegerField::new('medias')->hideOnForm(),
            BooleanField::new('isOnline'),

//            ImageField::new('media','')
//                ->setColumns(6)
//                ->setBasePath('assets/images')
//                ->setUploadDir('public/assets/images')
//                ->setUploadedFileNamePattern('[randomhash].[extension]')
//                ->setRequired(false),

//            ImageField::new('illustration2')
//                ->setColumns(6)
//                ->setBasePath('assets/images')
//                ->setUploadDir('public/assets/images')
//                ->setUploadedFileNamePattern('[randomhash].[extension]')
//                ->setRequired(false),
            // ColorField::new('parent')

        ];
    }
//      by_reference
//    Similarly, if you're using the CollectionType field where your underlying collection data is an object (like with Doctrine's ArrayCollection),
//    then by_reference must be set to false if you need the adder and remover (e.g. addAuthor() and removeAuthor()) to be called.
//    https://symfony.com/doc/current/reference/forms/types/collection.html#by-reference


    /**
     * createEntity permet de definir les valeurs par défaut de l'entité
     * @param string $entityFqcn
     * @return Article
     */
    public function createEntity(string $entityFqcn)
    {

        $article = new Article();
        $article->setCreatedAt( new \DateTimeImmutable() );
        $article->setUpdatedAt( new \DateTimeImmutable() );
        $article->setPosition( $this->entityManager->getRepository(Article::class)->count([]) + 1 );
//        if($entityId != null){
//            $article->setArticleId($entityId);
//        }
//
        return $article;
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
            $response->where('entity.article_id = :entity_id');
            $response->setParameter('entity_id', $this->entity->getId());
        }else{
            $response->where('entity.article_id IS NULL');
        }

        return $response;
    }


    /**
     * index renvois vers la page de base de la catégorie souvent lié à la liste
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
            $this->entity = $this->entityManager->getRepository(Article::class)->find($entityId);
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
        $entity = $context->getEntity()->getInstance();

        if($entity->getChildren()->isEmpty()){
            return parent::detail($context);
        }else {

            // Génération de l'URL, ajout en paramètre de l'id de la categorie.
            $url = $this->adminUrlGenerator->setAction(Action::INDEX)
                ->set('entityId', $entity->getId())
                ->generateUrl();

            // Redirection
            return $this->redirect($url);
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
            ->setEntityLabelInPlural('Articles')
            // Titre de la page et nom de la liste affichée
            ->setPageTitle('index', function (){
                if($this->entity != null){
                    return 'Article : '.$this->entity->getTitle();
                }
            })
            // Permet de choisir son template plutôt que le template général
            ->overrideTemplate('crud/index', 'backoffice/article/articles.html.twig')

            ->overrideTemplate('crud/detail', 'backoffice/article/article.html.twig')
            //showEntityActionsInlined : permet d'afficher les actions en ligne plutot que dans un menu
            ->showEntityActionsInlined()
            // Help : met une icône ? à coté du titre avec un text quand on passe la souris dessus
            ->setHelp('detail',"Message d'aide");
    }

    /**
     * @param KeyValueStore $responseParameters
     * @return KeyValueStore
     */
    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        // On récupère tous les articles
        $articles = $this->entityManager->getRepository(Article::class)->findAll();
        // On les envoie à la vue
        $responseParameters->set('articles',$articles);

        // On initialise medias a un array vide
        $medias = [];
        // On récupère les médias liés à l'article

        if( key_exists('entityId',$_GET) ) {
            $medias = $this->entityManager->getRepository(Media::class)->findby(array('article' => $_GET['entityId']));
        }
        if( $this->entity ){
            $medias = $this->entityManager->getRepository(Media::class)->findby(array('article' => $this->entity->getId()));
        }


        // On envoie les médias à la vue
        $responseParameters->set('medias',$medias);
        return parent::configureResponseParameters($responseParameters);
    }

    /**
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {

        // Bouton Revenir
        $returnAction = Action::new('returnAction', 'Revenir', 'fa fa-arrow-left');
        // renders the action as a <a> HTML element
        $returnAction->displayAsLink();

        if($this->parentEntity != null) {
            $returnAction->linkToRoute(Action::DETAIL, [
                'entityId' => $this->parentEntity->getId()
            ]);
        }else{
            $returnAction->linkToCrudAction('index');
        }
        $returnAction->createAsGlobalAction();
        $returnAction->addCssClass('btn btn-primary');

        $actions->add(Crud::PAGE_DETAIL, $returnAction);


        // Bouton Créer un paragraphe
        $addArticle = Action::new('addArticle', 'Créer paragraphe', 'fa fa-square-plus');
        // renders the action as a <a> HTML element
        $addArticle->displayAsLink();

        if($this->parentEntity != null) {

            $addArticle->linkToCrudAction(Action::NEW,[
                'entityId' => $this->parentEntity->getId()
            ]);
        }else{
            $addArticle->linkToCrudAction(Action::NEW);
        }
        $addArticle->createAsGlobalAction();
        $addArticle->addCssClass('btn btn-primary');
        $actions->add(Crud::PAGE_DETAIL, $addArticle);



        // Bouton Aperçu
        $apercu = Action::new('$apercu', 'Aperçu', 'fa fa-magnifying-glass');
        // renders the action as a <a> HTML element
        $apercu->displayAsLink();

        if($this->parentEntity != null) {
            $apercu->linkToRoute(Action::DETAIL, [
                'entityId' => $this->parentEntity->getId()
            ]);
        }else{
            $apercu->linkToCrudAction('index');
        }
        $apercu->createAsGlobalAction();
        $apercu->addCssClass('btn btn-primary');
        $actions->add(Crud::PAGE_DETAIL, $apercu);

        return $actions;
    }

}
