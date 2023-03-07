<?php

namespace App\Controller\Backoffice;

use App\Entity\Article;
use App\Entity\Category;
use App\Field\MediaField;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\RedirectResponse;
class ArticleCrudController extends AbstractCrudController
{

    private AdminUrlGenerator $adminUrlGenerator;
    private EntityManagerInterface $entityManager;
    private EntityRepository $entityRepository;
    private ?Article $parentEntity = null;
    private ?Article $entity = null;
    private ?Category $category = null;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, EntityRepository $entityRepository, EntityManagerInterface $entityManager)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->entityRepository  = $entityRepository;
        $this->entityManager     = $entityManager;
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
        yield FormField::addTab('Contenu');
        dump($this->entity);
        // abandonné : Les mediaspecs sont passés en paramètre à la vue.
        if(Crud::PAGE_EDIT === $pageName) {
            $mediaspecs = $this->entityManager->getRepository(Article::class)->getMediaspecs($this->entity);
            dump($mediaspecs);
            foreach($mediaspecs as $mediaspec){
                //yield ImageField::new('medias',$mediaspec->getName());
            }
        }
        yield IdField::new('id')->hideOnForm();
        yield IntegerField::new('ordre', 'ordre')->hideOnForm();
        yield DateField::new('created_at','création')->hideOnForm();
        yield DateField::new('updated_at','dernière édition')->hideOnForm();
        yield AssociationField::new('children','Enfants')->hideOnForm();
        yield BooleanField::new('isOnline', 'En ligne')->hideOnForm();

        yield TextField::new('title','titre')->setColumns(6);
        yield TextEditorField::new('content','description')->setColumns(12);
        yield AssociationField::new('parent','Article Parent')->hideOnDetail()->setColumns(6)->hideOnIndex()->setRequired(false);
        yield AssociationField::new('category','Catégorie Parent')->hideOnDetail()->setColumns(6)->hideOnIndex()->setRequired(false);;


        yield FormField::addTab('Medias')
            ->setIcon('image');

        yield ImageField::new('medias','')
            ->setColumns(6)
            ->setBasePath('assets/images')
            ->setUploadDir('public/assets/images')
            ->setUploadedFileNamePattern('[name]_[randomhash].[extension]')
            ->setRequired(false);

//            ImageField::new('illustration2')
//                ->setColumns(6)
//                ->setBasePath('assets/images')
//                ->setUploadDir('public/assets/images')
//                ->setUploadedFileNamePattern('[randomhash].[extension]')
//                ->setRequired(false);
        // ColorField::new('parent')




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
        $article->setOrdre( $this->entityManager->getRepository(Article::class)->count([]) + 1 );
//        if($entityId != null){
//            $article->setArticleId($entityId);
//        }
//
        return $article;
    }

    /**
     * createIndexQueryBuilder Requêtage des entités à afficher.
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
            $response->where('entity.article_id = :entity_id');
            $response->setParameter('entity_id', $this->entity->getId());

        }
        // Sinon on affiche tous les articles qui ne sont pas des sous articles.
        else
        {
            $response->where('entity.article_id IS NULL');
        }
        // Tri par ordre.
        $response->orderBy('entity.ordre');
        return $response;
    }


    /**
     * index Affiche la liste des enfants ou la liste des parents.
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
     * detail renvoi vers une page détaillant un des objets mis en bdd
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function detail(AdminContext $context)
    {
        // Récupération de l'article
        $entity = $context->getEntity()->getInstance();
        // Si il n'a pas d'enfants, on affiche le détail de l'article
        if($entity->getChildren()->isEmpty())
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
     * detail renvoi vers une page détaillant un des objets mis en bdd
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(AdminContext $context)
    {
        // Récupération de l'article
        $this->entity = $context->getEntity()->getInstance();
        // Si ce n'est pas un sous article on récupère sa categorie parent
        if($this->entity->getParent() == null)
        {
            $this->category = $this->entity->getCategory();
        }
        return parent::edit($context);
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
                }else if($this->category != null){
                    return 'Articles de la catégorie : '.$this->category->getTitle();
                }
            })
            // Surcharge des templates de base.
            ->overrideTemplate('crud/index', 'backoffice/article/articles.html.twig')
            ->overrideTemplate('crud/detail', 'backoffice/article/article.html.twig')
            ->overrideTemplate('crud/edit', 'backoffice/article/edit.html.twig')

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
        $parent             =   null;
        $grandParentId      =   null;
        $crudController     =   'Category';
        $keyName            =   'entityId';
        // Si on affiche une liste d'article
        if (Crud::PAGE_INDEX === $responseParameters->get('pageName')) {

            // Si on est sur la liste des articles d'une catégorie.
            if ($this->category != null) {
                $parent         =   $this->category;
                // Envoi de l'id du grand-parent à la vue.
                $grandParentId  =   $this->category->getParent()?->getId();
            }
            // Si on est sur la liste des sous articles d'un article.
            else if ($this->entity != null) {
                $parent         =   $this->entity;
                // Envoi de l'id du grand-parent à la vue.
                $grandParentId  =   $this->entity?->getCategory()?->getId();
                $crudController =   'Article';
                $keyName        =   'categoryId';
            }
        }
        else if (Crud::PAGE_EDIT === $responseParameters->get('pageName')) {
            $crudController     =   'Article';
            // Si on est sur la liste des articles d'une catégorie.
            if ($this->category != null) {
                // Envoi de l'id du grand-parent à la vue.
                $grandParentId  =   $this->category->getId();
                $keyName        =   'categoryId';
            } // Si on est sur la liste des sous articles d'un article.
            else if ($this->entity != null) {
                // Envoi de l'id du grand-parent à la vue.
                $grandParentId  =   $this->entity->getParent()?->getId();
            }
            // Envoi des mediaspecs à la vue
            $responseParameters->set('mediaspecs', $this->entityManager->getRepository(Article::class)->getMediaspecs($this->entity));

        }
        // Passage des variables dans la vue
        $responseParameters->set('parent', $parent);
        $responseParameters->set('crudController', $crudController);
        $responseParameters->set('grandParentId', $grandParentId);
        $responseParameters->set('keyName', $keyName);


        // On initialise medias a un array vide
        $medias = [];
        // On récupère les médias liés à l'article

//        if( key_exists('entityId',$_GET) ) {
//            $medias = $this->entityManager->getRepository(Media::class)->findby(array('article' => $_GET['entityId']));
//        }
//        if( $this->entity ){
//            $medias = $this->entityManager->getRepository(Media::class)->findby(array('article' => $this->entity->getId()));
//        }


        // On envoie les médias à la vue
        $responseParameters->set('medias',null);
        return parent::configureResponseParameters($responseParameters);
    }

    /**
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
        $returnPageAction->linkToCrudAction('edit');
        $returnPageAction->addCssClass('btn btn-primary');
        // Ajout des boutons à la liste des actions disponibles.
        $actions->add(Crud::PAGE_EDIT, $returnPageAction);

        // Bouton Revenir
//        $returnAction = Action::new('return', 'Revenir', 'fa fa-arrow-left');
//        // renders the action as a <a> HTML element
//        $returnAction->displayAsLink();
//        $actions->add(Crud::PAGE_DETAIL, $returnAction);



        // Bouton Aperçu
//        $apercu = Action::new('apercu', 'Aperçu', 'fa fa-magnifying-glass');
//        // renders the action as a <a> HTML element
//        $apercu->displayAsLink();
//
//        if($this->parentEntity != null) {
//            $apercu->linkToRoute(Action::DETAIL, [
//                'entityId' => $this->parentEntity->getId()
//            ]);
//        }else{
//            $apercu->linkToCrudAction('index');
//        }
//        $apercu->createAsGlobalAction();
//        $apercu->addCssClass('btn btn-primary');
//        $actions->add(Crud::PAGE_EDIT, $apercu);


        return $actions;
    }

}
