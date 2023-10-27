<?php

namespace App\Controller\Backoffice;

use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\ArticleData;
use App\Entity\Category;
use App\Entity\Language;
use App\Entity\Media;
use App\Entity\MediaType;
use App\Entity\Online;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use PHPUnit\TextUI\XmlConfiguration\Constant;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mime\MimeTypes;

class GalleryCrudController extends ArticleCrudController
{
    // Variables

    // Article courant
    protected ?Article $entity = null;
    // Categorie parent.
    protected ?Category $category = null;
    public string $model_type_label   =   'image';

    // Type de fichier accepté par le média
    public MediaType $mediaType;
    public function __construct(
        // Services

        // Générateur de routes
        protected AdminUrlGenerator $adminUrlGenerator,
        // Gestionnaire d'entité Symfony
        protected EntityManagerInterface $entityManager,
        // Repository EasyAdmin
        protected EntityRepository $entityRepository,
        // Code Langue
        protected string $locale,
        protected RequestStack $requestStack,
    )
    {
        // Récupération du type de média
        $this->mediaType = $this->entityManager->getRepository(MediaType::class)->findOneBy(['label' => $this->model_type_label]);

        $this->category = $this->entityManager->getRepository(Category::class)->findOneBy(['id' => $_ENV['GALLERY_CATEGORY_ID']]);

        // Appel du constructeur du controller parent
        parent::__construct($this->adminUrlGenerator, $this->entityManager, $this->entityRepository, $this->locale, $this->requestStack);
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
        // Ordre
        if($this->category != null) {
            $this->category->getDatas($this->locale);
            if(method_exists($this->category, 'getHasOrdre') && $this->category->getHasOrdre()){
                yield BooleanField::new('ordre', 'ordre')->hideOnForm()
                    ->setTemplatePath('backoffice/field/order.html.twig');
            }
        }
        // Autres vue liste
        yield TextField::new('title','Nom')->setColumns(4);
        yield BooleanField::new('isOnline', 'En ligne')->hideOnForm();
        yield DateField::new('created_at','création')->hideOnForm();
        yield DateField::new('updated_at','dernière édition')->hideOnForm();

        // Champs pour l'édition et la création d'un article.
        if(in_array($pageName, [Crud::PAGE_EDIT, Crud::PAGE_NEW])) {

            // On récupère les catégories auxquelles on peut associer des articles.
            $category_form_options = [
                // Catégorie associée.
                'data'    => $this->category,
                'choices' => [$this->category]
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
            $categoryParent = $this->category;
            // Récupération des langues
            $languages = $this->entityManager->getRepository(Language::class)->getAllForChoices();
            // Si on a plusieurs langues actives.
            if(count($languages) > 1) {
                // Sélecteur de langue pour édition du contenu en fonction de la langue
                yield LanguageSelectField::new('language', 'langue')->setChoices(
                    $this->entityManager->getRepository(Language::class)->getAllForChoices()
                );
            }

            // EXTRA FIELDS VUE FORM
            // Ajout des formulaires d'ajout de médias en fonction des mediaspecs qui s'appliquent à l'entité
            foreach($this->getExtraFieldsForForm($categoryParent) as $extraField){
                yield $extraField;
            }

            // MEDIAS
            // Ajout d'un onglet
            yield FormField::addTab('Depôt')
                ->setIcon('download');
            yield  Field::new('galleryMediaUploads', 'Photos ( 2302 x 1548 )')
                ->setFormTypeOptions([
                    'block_name' => 'gallery_edit',
                ])->setCustomOptions([
                ])
            ;
            yield FormField::addTab('Photos')
                ->setIcon('image');
            yield  Field::new('galleryMedias', 'Photos')
                ->setFormTypeOptions([
                    'block_name' => 'gallery_show',
                    'data' => [
                        'photos' => $this->entityManager->getRepository(Media::class)->getPhotos($this->entity),
                        'article' => $this->entity,
                    ]
                ]);


        }// Fin si PAGE_EDIT

    }

    /**
     * Fourni à la vue les variables dont elle a besoin pour fonctionner.
     *
     * @param KeyValueStore $responseParameters
     * @return KeyValueStore
     */
    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        // SURCHARGE
        // !! Passage en global pour que le champ de formulaire puisse y accéder !!
        $twig = $this->container->get('twig');
        // Dossier temporaire pour l'upload des fichiers
        $twig->addGlobal('folderId', 'gallery-'.$this->getUser()->getId());

        // APPEL DU PARENT ArticleCrudController
        return parent::configureResponseParameters($responseParameters);
    }

    /**
     *  Supprime un media d'un article
     *
     * @param AdminContext $context
     * @return JsonResponse
     */
    public function removeMedia(AdminContext $context)
    {
        $response = array(
            'error'             => null,
            'mediaId'           => null,
        );

        if($this->entityManager->getRepository(Media::class)->removeById($context->getRequest()->get('mediaId'), true)){
            $response['mediaId']    =   $context->getRequest()->get('mediaId');
        }else{
            $response['error']      =   'Impossible de supprimer la photo';
        }
        // Retour
        return new JsonResponse($response);
    }

    /**
     *  Supprime un media d'un article
     *
     * @param AdminContext $context
     * @return JsonResponse
     */
    public function starMedia(AdminContext $context)
    {
        $response = array(
            'error'             => null,
            'mediaId'           => null,
        );

        if($this->entityManager->getRepository(Article::class)->starMedia(
            $context->getRequest()->get('articleId'),
            $context->getRequest()->get('mediaId')
        )){
            $response['mediaId']    =   $context->getRequest()->get('mediaId');
        }else{
            $response['error']      =   'Impossible de mettre en vedette la photo';
        }
        // Retour
        return new JsonResponse($response);
    }

    /**
     * Renvoi vers le formulaire d'édition de l'article
     *
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|Response
     */
    public function edit(AdminContext $context)
    {
//        $fileSystem = new Filesystem();
//        $uploadPath = Constants::DYN_UPLOAD_PATH . 'gallery-'.$this->getUser()->getId();
//        // On vide le dossier d'upload de l'utilisateur
//        if($fileSystem->exists($uploadPath)){
//            $finder = new Finder();
//            $fileSystem->remove($finder->files()->in($uploadPath));
//        }
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
     * Définie les assets nécessaires pour le controleur de médias.
     * @param Assets $assets
     * @return Assets
     */
    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addWebpackEncoreEntry(Asset::new('bo_gallery')->ignoreOnIndex())
            ->addWebpackEncoreEntry(Asset::new('bo_articles')->onlyOnIndex());
    }

}
