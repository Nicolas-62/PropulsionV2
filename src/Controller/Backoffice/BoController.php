<?php

namespace App\Controller\Backoffice;

use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Media;
use App\Entity\MediaLink;
use App\Field\MediaSelectField;
use App\Service\Secure;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Spatie\PdfToImage\Pdf;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mime\MimeTypes;


abstract class BoController extends AbstractCrudController
{
    // Objet reponse retourné lors des appels Ajax
    public array $response = array(
        'error'             => null,
        'folderId'          => null,
        'filename'          => null,
        'url'               => null,
        'thumbUrl'          => null,
    );

    public function __construct(
    )
    {
    }


    public function editOrdre(AdminContext $context, AdminUrlGenerator $adminUrlGenerator)
    {
        // On récupère l'entité
        $entity = $this->entityManager->getRepository($this->getEntityFqcn())->find($context->getRequest()->get('entityId'));
        // On met à jour son ordre
        $this->entityManager->getRepository($this->getEntityFqcn())->setOrdre($entity, $context->getRequest()->get('direction'));
        // On redirige vers la liste des entités
        $url = $adminUrlGenerator->unset('direction')->unset('entityId')->setAction(Action::INDEX)->generateUrl();
        return $this->redirect($url);
    }


    /**
     * Enregistre sur le serveur le fichier déposé par l'utilisateur
     *
     * @param AdminContext $context
     * @return JsonResponse
     */
    public function upload(Secure $secureService, AdminContext $context): JsonResponse
    {

        // Récupération du fichier
        $file = $context->getRequest()->files->get('file');
        // Si l'image a été récupérée.
        if(isset($file)){
            // Récupération du nom de dossier d'upload
            $folderId = $context->getRequest()->get('folderId');
            // Si il n'est pas défini on en créer un temporaire
            if( ! isset($folderId)) {
                // Dossier temporaire du fichier = chaine alphanumérique de 10 caractères.
                $folderId       =   $secureService->random_hash(5);
            }

            if ($file->isValid()) {
                // Chemin temporaire du fichier
                $fileBasePath = Constants::DYN_UPLOAD_PATH . $folderId . '/';
                // Nom du fichier
                $filename = $file->getClientOriginalName();

                // Déplacement du fichier dans le dossier temporaire
                if ($file->move($fileBasePath, $filename)) {

                    // Si le fichier est un pdf
                    $mimeType = new MimeTypes();
                    if ($mimeType->guessMimeType($fileBasePath . $filename) == 'application/pdf') {
                        // Nom de la vignette PDF
                        $thumb_filename = pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
                        // On créer un vignette de la première page du pdf
                        $pdf = new Pdf($fileBasePath . $filename);
                        $pdf->saveImage($fileBasePath . $thumb_filename);
                        // Chemin de la vignette
                        $this->response["thumbUrl"] = $context->getRequest()->getBaseUrl() . $fileBasePath . $thumb_filename;
                    }
                    // Données renvoyées à la vue
                    $this->response["folderId"] = $folderId;
                    $this->response["filename"] = $filename;
                    $file = new File($fileBasePath . $filename);
                    $this->response["filesize"] = $file->getSize();
                    // Chemin de l'image
                    $this->response["url"] = $context->getRequest()->getBaseUrl() . $fileBasePath . $filename;
                }
            } else {
                $this->response["error"] = "Impossible de récupérer le fichier";
            }

        }

        // Retour
        return new JsonResponse($this->response);
    }

    /**
     * Supprime sur le serveur le fichier déposé par l'utilisateur
     *
     * @param AdminContext $context
     */
    public function deleteUpload(AdminContext $context, AdminUrlGenerator $adminUrlGenerator)
    {


        // Récupération du nom du fichier à supprimer.
        $filename = $context->getRequest()->get('filename');
        // Récupération du dossier temporaire.
        $folderId = $context->getRequest()->get('folderId');

        // Si le nom du dossier et le nom de fichier sont définis.
        if(trim($folderId) != '') {
            // Chemin temporaire du dossier
            $folderPath = Constants::DYN_UPLOAD_PATH . $folderId . '/';
            $filesystem = new Filesystem();
            // Si le dossier existe.
            if ($filesystem->exists($folderPath)) {
                // Si pas de fichier renseigné
                if( ! $filename) {
                    // Suppression du dossier.
                    $filesystem->remove($folderPath);
                }else{
                    // Chemin temporaire du fichier
                    $filePath = $folderPath . $filename;
                    // Si le fichier existe
                    if($filesystem->exists($filePath)){
                        // Suppression du fichier.
                        $filesystem->remove($filePath);
                    }else{
                        $this->response["error"] = "Le fichier n'existe pas";
                    }
                }
            } else {
                $this->response["error"] = "Le dossier n'existe pas";
            }
        }
        // IF AJAX
        if( $context->getRequest()->isXmlHttpRequest()){
            $this->response["filename"] = $filename;
            $this->response["folderId"] = $folderId;
            // Retour
            return new JsonResponse($this->response);
        }else {
            // Récupération de l'id de l'entité
            $entityId = $context->getRequest()->get('entityId');
            if ($entityId != null) {
                $crudAction = Action::EDIT;
            } else {
                $crudAction = Action::NEW;
            }
            $url = $adminUrlGenerator->setAction($crudAction)
                ->set('entityId', $entityId)
                ->unset('folderId')
                ->generateUrl();
            // Retour
            return $this->redirect($url);
        }
    }

    /**
     * Construits les champs extra du formulaire d'édition d'un article
     * @return array
     */
    protected function getExtraFieldsForForm(?Category $categoryParent): array
    {
        // Entité article.
        $model = new Article();
        // Liste des champs à retourner.
        $extraFields = array();
        // Si la catégorie parent existe.
        if($categoryParent != null) {
            // Récupération des datas de la catégorie.
            $categoryParent->getDatas($this->locale);
            $extraFields[] =  FormField::addPanel('Contenu');

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
                    $extraFields[] = $model->getEasyAdminFieldType($extraField['ea_type'])::new($extraField['name'], $extraField['label'])
                        ->setColumns((int) $extraField['column']);
                }
            }
        }

        return $extraFields;
    }

    /**
     * Construit les champs d'ahout de media on fonction des médiaspec qu s'appliquent à l'élément.
     * @return array
     */
    protected function getMediaFieldsForForm(): array
    {
        // Liste des champs à retourner.
        $mediaFields = array();
        // Si l'entité peut posséder des fichiers
        if($this->entityManager->getRepository($this->getEntityFqcn())->hasFiles($this->entity)){
            // Ajout d'un onglet pour les fichiers
            $mediaFields[] = FormField::addTab('Fichiers')
                ->setIcon('file');

            $mediaFields[] = ChoiceField::new('mediaLinks', 'Fichiers')->setColumns(6)
                ->setFormTypeOptions([
                    'multiple' => true,
                    'choices' => $this->entityManager->getRepository(Media::class)->getAllFilesForChoices(),
                    'data' => $this->entityManager->getRepository(MediaLink::class)->getFilesByEntityForChoices($this->entity),
                    'mapped' => false,
                ])
            ;

        }

        // En édition on peut ajouter/enlever des médias.
        // Médiaspecs appliquées à l'entité
        $mediaspecs = $this->entityManager->getRepository($this->getEntityFqcn())->getMediaspecs($this->entity);
        // Si elles existent.
        if ($mediaspecs != null) {

            // MEDIAS
            // Ajout d'un onglet
            $mediaFields[] = FormField::addTab('Medias')
                ->setIcon('image');
            // Pour chaque mediaspec
            foreach ($mediaspecs as $index => $mediaspec) {
                // Ajout d'une ligne
                $mediaFields[] =  FormField::addRow();
                // Ajout d'un champ d'upload d'un média
                $imageField = Field::new('media' . ($index + 1), ucfirst($mediaspec->getLabel()) . ' : téléchargez un média ou...');
                $imageField->setColumns(8);
                // Récupération du média.
                $media = $this->entityManager->getRepository($this->getEntityFqcn())->getMedia($this->entity, $mediaspec);
                // Si l'entité possède un média pour cette mediaspec.
                if ($media != null) {
                    $imageField->setLabel(ucfirst($mediaspec->getLabel()))
                        // On associe le média existant au champ configuré
                        ->setValue($media)
                        // On définit la vue dédiée à l'affichage du média
                        ->setFormTypeOptions([
                            'block_name' => 'media_delete',
                        ])
                    ;
                    $mediaFields[] =  $imageField;
                }
                // Si pas encore de média défini.
                else {
                    // Ajout d'une zone d'upload de fichier
                    $imageField
                        ->setFormTypeOptions([
                            'block_name' => 'media_edit',
                        ])->setCustomOptions([
                            'cropWidth' => $mediaspec->getWidth(),
                            'cropHeight' => $mediaspec->getHeight(),
                        ])
                    ;
                    $mediaFields[] =  $imageField;
                    // Ajout d'un champ supplémentaire de sélection d'un média existant.
                    $mediaFields[] =  MediaSelectField::new('media' . ($index + 11))
                        ->setChoices(
                            $this->entityManager->getRepository(Media::class)->getAllForChoices()
                        )->setColumns(4);
                    ;
                }
            }
        }

        return $mediaFields;
    }


    /**
     * Retourne le nom du modèle géré par le controleur.
     * ex : App\Entity\Media => media
     *
     * @return string
     */
    public  function getModelName(): string
    {
        return strtolower( (new \ReflectionClass(Media::class))->getShortName() );
    }

    /**
     * Retourne le nom du modèle géré par le controleur.
     * ex : App\Entity\Media => media
     *
     * @return string
     */
    public  function getControllerName(): string
    {
        return (new \ReflectionClass($this->getContext()->getCrud()->getControllerFqcn()))->getShortName();
    }
}