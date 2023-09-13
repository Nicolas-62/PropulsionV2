<?php

namespace App\Controller\Backoffice;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Language;
use App\Entity\Media;
use App\Entity\Seo;
use App\Field\LanguageSelectField;
use App\Field\MediaSelectField;
use App\Form\SeoType;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class ArticleCrudController extends BoController
{
    // Variables

    // Article courant
    protected ?Article $entity = null;
    // Categorie parent.
    protected ?Category $category = null;

    public function __construct(
        // Services

        // Générateur de routes
        protected AdminUrlGenerator $adminUrlGenerator,
        // Gestionnaire d'entité Symfony
        protected EntityManagerInterface $entityManager,
        // Repository EasyAdmin
        protected EntityRepository $entityRepository,
        // Code Langue
        protected string $locale
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
     * Permet la configuration des différents champs que l'on va retrouver sur les pages du crud
     *
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        // Entité article.
        $model = new Article();

        // Onglet
        yield FormField::addTab('Paramètres');
        // Champs communs à plusieurs actions (liste, edition, detail, formulaire...)
        yield IdField::new('id')->hideOnForm()->setPermission('ROLE_DEV');
        yield IntegerField::new('ordre', 'ordre')->hideOnForm();
        yield TextField::new('title','titre')->setColumns(4);
        //yield SlugField::new('slug', 'Url')->setTargetFieldName('title')->hideOnIndex();
        yield AssociationField::new('children','Enfants')->hideOnForm();
        yield AssociationField::new('category','Categorie')->setColumns(4)->hideOnForm()->formatValue(function($value, $article) {
            // Concatenation du nom de la catégorie avec les noms des catégories parentes.
            $category = $article->getCategory();
            if($category != null) {
                $value = $category->getTitle();
                foreach($category->getAncestors() as $ancestor) {
                    $value = $ancestor->getTitle() . ' / '. $value;
                }
            }
            return $value;
        });
        yield AssociationField::new('parent','Article Parent')->setColumns(4)->hideOnForm()->formatValue(function($value, $article) {
            // Concatenation du nom de l'article parent avec les noms des articles parents.
            $parent = $article->getParent();
            if($parent != null) {
                $value = $parent->getTitle();
                foreach($parent->getAncestors() as $ancestor) {
                    if($ancestor instanceof Article){
                        $value = $ancestor->getTitle() . ' / ' . $value;
                    }
                }
            }
            return $value;
        });

        yield BooleanField::new('isOnline', 'En ligne')->hideOnForm();
        yield DateField::new('created_at','création')->hideOnForm();
        yield DateField::new('updated_at','dernière édition')->hideOnForm();

        // Champs pour l'édition et la création d'un article.
        if(in_array($pageName, [Crud::PAGE_EDIT, Crud::PAGE_NEW])) {

            // On récupère les articles qui peuvent avoir des sous articles.
            $hasCreateArticles = $this->entityManager->getRepository(Article::class)->getHasSubArticleArticles($this->entity, $this->locale);
            // Article parent
            yield AssociationField::new('parent', 'Article Parent')->hideOnDetail()->setColumns(3)->hideOnIndex()->setRequired(false)->setFormTypeOptions([
                // Article parent associé.
                'data' => $this->entity?->getParent(),
                // Choix possibles.
                'choices' =>$hasCreateArticles
            ]);

            // On récupère les catégories auxquelles on peut associer des articles.
            $category_form_options = [
                // Catégorie associée.
                'data' => $this->category,
                // Choix possibles.
                'choices' => $this->entityManager->getRepository(Category::class)->getHasCreateCategories($this->getUser(), $this->locale),
                // On ajoute dans le label le nom des ancètres
                'choice_label' => function($category, $key, $value) {
                    $value = $category->getTitle();
                    foreach($category->getAncestors() as $ancestor) {
                        $value = $ancestor->getTitle() . ' / '. $value;
                    }
                    return $value;
                },
            ];
            // Catégorie parent
            yield AssociationField::new('category', 'Catégorie Parent')
                ->hideOnDetail()->setColumns(3)->hideOnIndex()->setRequired(false)->setFormTypeOptions($category_form_options)
            ;


        }// Fin si PAGE_EDIT ou PAGE_NEW

        // Si l'article existe déjà on peut éditer le contenu en fonction de la langue
        if($pageName === Crud::PAGE_EDIT) {

            // Si on est en édition, l'ajout des thèmes dépend de la config sur la catégorie parent.
            // Récupération de la première catégorie parent pour appliquer la configuration des champs.
            $categoryParent = $this->entity->getCategoryParent();

            // Si thèmes actifs.
            if ($categoryParent != null && $categoryParent->hasTheme()) {
                // Themes
                yield AssociationField::new('themes', 'Thèmes')->setRequired(false);
            }

            // MEDIAS
            // Ajout des formulaires d'ajout de médias en fonction des mediaspecs qui s'appliquent à l'entité
            foreach($this->getMediaFields() as $mediaField){
                yield $mediaField;
            }

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
            if($this->entityManager->getRepository(Article::class)->hasSeo($this->entity)){
                // Récupération de la seo de la langue courante.
                $seo = $this->entity->getSeo($this->locale);
                // Si la seo n'existe pas on retourne un objet vide.
                if($seo == null){
                    $seo = new Seo();
                    // On récupère la SEO par son code langue
                    $language  = $this->entityManager->getRepository(Language::class)->findOneBy(['code' => $this->locale]);
                    $seo->setLanguage($language);
                }
                //yield FormField::addPanel('Seo');
                //yield TextField::new('title','titre')->setColumns(4);
                yield CollectionField::new('seo', 'SEO')
                    ->setEntryIsComplex()
                    ->allowDelete(false)
                    ->allowAdd(false)
                    ->renderExpanded()
                    ->setLabel(false)
                    ->setFormTypeOptions([
                        // Passage de la seo dans les champs du formulaire
                        'data' => ['seo' => $seo],
                    ])
                    ->setEntryType(SeoType::class)
                ;
            }


            // EXTRA FIELDS VUE FORM
            // Si la catégorie parent existe.
            if($categoryParent != null) {
                // Récupération des datas de la catégorie.
                $categoryParent->getDatas($this->locale);
                yield FormField::addPanel('Contenu');

                // Ajout des champs spécifiques à l'instance définis dans l'entité.
                foreach ($model->getExtraFields() as $extraField) {
                    $show_field = false;
                    // si activé depuis la catégorie parent.
                    if(method_exists($categoryParent, 'getHas'.ucfirst($extraField['name'])) ){
                        if($categoryParent->{'getHas' . ucfirst($extraField['name'])}() ){
                            $show_field = true;
                        }
                    }else{
                        $show_field = true;
                    }
                    // Si on affiche le champ
                    if($show_field){
                        yield $field = $model->getEasyAdminFieldType($extraField['ea_type'])::new($extraField['name'], $extraField['label'])
                            ->setColumns((int) $extraField['column']);
                        // Si le champs est une date
                        if(str_contains($extraField['name'], 'date')){

                            //$field->setCustomOption('data', $this->entity->{'get'.ucfirst($extraField['name'])}()->format($extraField['format']));

                        }
                    }
                }
            }

        }// Fin si PAGE_EDIT

        // EXTRA FIELDS VUE LIST
        // Définition des champs extras visibles en vue liste.
        if($pageName === Crud::PAGE_INDEX) {
            // Ajout des champs spécifiques à l'instance définis dans l'entité.
            foreach ($model->getExtraFields() as $extraField) {

                // Si visible en vue liste.
                if ($extraField['list'] !== 'false') {
                    yield $model->getEasyAdminFieldType($extraField['ea_type'])::new($extraField['name'], $extraField['label'])
                        ->setColumns((int) $extraField['column'])->formatValue(function($value, $entity) use($extraField){
                            $entity->getDatas($this->locale);
                            return $entity->{'get' . ucfirst($extraField['name'])}();
                        });
                }
            }
        }// Fin si PAGE_INDEX

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
        // Récupération de l'id de la catégorie si on vient d'une catégorie.
        $referrer   =   $this->adminUrlGenerator->get('referrer');
        // Si on créer un article depuis une catégorie
        if($referrer != null) {
            $categoryId = Request::create($referrer)->get('categoryId');
            if($categoryId != null){
                // On pré-rempli le champ category.
                $this->category = $this->entityManager->getRepository(Category::class)->find($categoryId);
            }
        }

        $article = new Article();
        $article->setCreatedAt( new DateTimeImmutable() );
        $article->setUpdatedAt( new DateTimeImmutable() );
        $article->setOrdre( $this->entityManager->getRepository(Article::class)->count([]) + 1 );

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
            $response->andWhere('entity.category = :element');
            $response->setParameter('element', $this->category->getId());
        }
        // Si on affiche les sous articles d'un article
        else if($this->entity != null)
        {
            $response->andWhere('entity.article_id = :entity_id');
            $response->setParameter('entity_id', $this->entity->getId());

        }
        // Si pas d'ordre
        if($searchDto->getSort() == [])
        {
            // Ordonne par ordre
            $response->orderBy('entity.created_at', 'DESC');
        }
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
        if($action == Action::INDEX) {
            // Génération de l'URL
            $url = $this->adminUrlGenerator->setAction(Action::INDEX)
                ->unset('entityId')
                ->generateUrl();
            // Redirection
            return $this->redirect($url);
        }else{
            return parent::getRedirectResponseAfterSave( $context, $action);
        }
    }

    /**
     * Permet de configurer le crud, champs de recherche, redirection vers un template spécial, triage ...
     *
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
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
            ->setFormThemes([
                'backoffice/article/media_edit.html.twig',
                'backoffice/article/media_delete.html.twig',
                'backoffice/article/media_select.html.twig',
                'backoffice/article/seo_edit.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig'
                ]
            )

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
        // nom du champ du modèle parent du parent pour retour à la liste
        $ancestorKeyName        =   'entityId';
        // nom du champ du modèle parent sur lequel filtrer.
        $searchKeyName          =   'categoryId';

        // Si on affiche une liste d'article
        if (Crud::PAGE_INDEX === $responseParameters->get('pageName')) {
            // Si on est sur la liste des articles d'une catégorie.
            if ($this->category != null) {
                $parentId               =   $this->category->getId();
                // Envoi de l'id du grand-parent à la vue.
                $ancestorId             =   $this->category->getParent()?->getId();
            }
            // Si on est sur la liste des sous articles d'un article.
            else if ($this->entity != null) {
                $parentId               =   $this->entity->getId();
                // Envoi de l'id du grand-parent à la vue.
                $ancestorId             =   $this->entity->getCategory()?->getId();
                $crudControllerName     =   'Article';
                $ancestorKeyName        =   'categoryId';
                $searchKeyName          =   'entityId';
            }
        }
        // Si édite un article
        else if (Crud::PAGE_EDIT === $responseParameters->get('pageName')) {
            $crudControllerName     =   'Article';
            // Si on est sur la liste des articles d'une catégorie.
            if ($this->category != null)
            {
                // Envoi de l'id du parent à la vue.
                $ancestorId         =   $this->category->getId();
                $ancestorKeyName    =   'categoryId';
            } // Si on est sur la liste des sous articles d'un article.
            else if ($this->entity != null)
            {
                // Envoi de l'id du parent à la vue.
                $ancestorId     =   $this->entity->getParent()?->getId();
            }
            // Envoi des mediaspecs à la vue
            $responseParameters->set('mediaspecs', $this->entityManager->getRepository(Article::class)->getMediaspecs($this->entity));

        }
        // Passage des variables dans la vue
        $responseParameters->set('parentId', $parentId);
        $responseParameters->set('crudControllerName', $crudControllerName);
        $responseParameters->set('ancestorId', $ancestorId);
        $responseParameters->set('ancestorKeyName', $ancestorKeyName);
        $responseParameters->set('searchKeyName', $searchKeyName);

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
