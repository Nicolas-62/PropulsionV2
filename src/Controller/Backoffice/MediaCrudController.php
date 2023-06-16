<?php

namespace App\Controller\Backoffice;

use App\Constants\Constants;
use App\Entity\Category;
use App\Entity\Media;
use App\Field\MediaUploadField;
use App\Service\Secure;
use App\Service\Toolbox;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
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
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * @method UploadedFile move()
 */
class MediaCrudController extends AbstractCrudController
{


    public function __construct(
        // Services

        // Gestionnaire d'entité Symfony
        private EntityManagerInterface $entityManager,
        private Secure                 $secureService,
        private Toolbox                $toolbox
    )
    {
    }
    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    /**
     * Configure les champs à afficher dans les interfaces.
     *
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        // LISTE
        // Nom du média.
        yield TextField::new('getName', 'nom')->onlyOnIndex();


        yield TextField::new('legend', 'description');
// TEST VICHUPLOAD BUNDLE
//        yield ImageField::new('media', 'Image')
//            ->onlyOnIndex()
//            ->setBasePath('/assets/dyn-upload');
//        yield MediaUploadField::new('mediaFile', 'Image')
//            ->onlyOnForms()
//            ->setFormType(VichImageType::class);

        // Vignette du média dans la liste des médias.
        yield MediaUploadField::new('media', 'Image')->onlyOnIndex();

        // FORMULAIRE

        // Champ personnalisé d'upload du média
        $mediaField = MediaUploadField::new('media', 'Image')
            ->onlyOnForms()
            ->setRequired(true)
            ->setFormTypeOptions([
                'block_name' => 'media_edit',
            ])
        ;

//        // Si le média existe déjà.
//        if(Crud::PAGE_EDIT === $pageName) {
//            // On personnalise la vue, on affiche l'image.
//            $mediaField->setFormTypeOptions([
//                'block_name' => 'media_edit',
//            ]);
//        }

        yield $mediaField;

    }


    /**
     * Renvoi vers le formulaire d'édition du media
     *
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
        $media = new Media();
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
            ->setEntityLabelInPlural('Medias')
            // Titre de la page et nom de la liste affichée
            ->setHelp('index', 'Liste des médias')
            // Template personnalisé
            ->overrideTemplate('crud/index', 'backoffice/media/medias.html.twig')

            // Pagination
            ->setPaginatorPageSize(12)
            ->setPaginatorRangeSize(4)
            // Personnalisation du formulaire
            ->setFormThemes(['backoffice/form/media_edit.html.twig', '@EasyAdmin/crud/form_theme.html.twig'])
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
        // Surcharge du bouton de suppression du média, vérification si il est lié à des articles/catégories
        $actions->update(Crud::PAGE_INDEX,'delete', function(Action $action){
            return $action->setTemplatePath('backoffice/media/delete_action.html.twig');
        });

        return $actions;
    }


    /**
     * Enregistre sur le serveur le fichier déposé par l'utilisateur
     *
     * @param EntityManagerInterface $entityManager
     * @param AdminContext $context
     * @return JsonResponse
     */
    public function upload(EntityManagerInterface $entityManager, AdminContext $context): JsonResponse
    {
        // Objet réponse.
        $response = array('error' => null, 'folderId' => null, 'filename' => null);

        // Récupération de l'image
        // DEBUG
        dump($context->getRequest()->files->get('file'));
        $file = $context->getRequest()->files->get('file');

        // Si l'image a été récupérée.
        if(isset($file) && $file->isValid()) {
            // Dossier temporaire de l'image = chaine alphanumérique de 10 caractères.
            $folderId = $this->secureService->random_hash(5);
            // Chemin temporaire de l'image
            $imageBasePath = Constants::ASSETS_UPLOAD_PATH . $folderId . '/';
            // Déplacement de l'image dans le dossier temporaire
            if($file->move($imageBasePath, $file->getClientOriginalName())){
                $response["folderId"] = $folderId;
                $response["filename"] = $file->getClientOriginalName();
            }
        }else{
            $response["error"] = "Impossible de récupérer le fichier";
        }
        // Retour
        return new JsonResponse($response);
    }


   public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
   {
       // Récupération de l'id du dossier temporaire
       $folderId = $this->getContext()->getRequest()->get('folderId');
       // Récupération du nom du fichier
       $filename = $this->getContext()->getRequest()->get('filename');
       // Si un fichier a été déposé
       if($folderId != null){
           $tmpPath = Constants::ASSETS_UPLOAD_PATH . $folderId . '/';
            // Chemin du fichier temporaire.
           $imageTmpPath = $tmpPath. '/'.$filename;

           // Si le fichier existe.
           $filesystem = new Filesystem();
           if($filesystem->exists($imageTmpPath)){
                // Infos de l'image
               $file = new File($imageTmpPath);
               // On néttoie le nom de l'image.
               $new_filename = $this->toolbox->url_compliant($file->getBasename('.' . $file->getExtension())).'-'.time().'.'.$file->guessExtension();
               // Chemin de destination
               $imagePath = Constants::ASSETS_IMG_PATH .$new_filename;
               // Si on arrive à le déplacer dans la mediatheque.
               try{
                   $filesystem->rename($imageTmpPath, $imagePath);
               }catch (IOException $e){
                   $this->addFlash('error', "Impossible de sauvegarder l'image dans la médiatheque");
               } finally {
                   $filesystem->remove($tmpPath);
               }
               $entityInstance->setMedia($new_filename);
           }
       }

       parent::persistEntity($entityManager, $entityInstance); // TODO: Change the autogenerated stub
   }


    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addWebpackEncoreEntry('bo_medias');
    }



}
