<?php

namespace App\Controller\Backoffice;

use App\Constants\Constants;
use App\Entity\Article;
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
        // Objet réponse.
        $response = array(
          'error' => null,
          'folderId' => null,
          'filename' => null,
          'imageSelector' => null,
           'url' => null,
           'thumbUrl' => null,
        );

        // Récupération du fichier
        $file = $context->getRequest()->files->get('file');

        // Si l'image a été récupérée.
        if(isset($file) && $file->isValid()) {
            // Dossier temporaire du fichier = chaine alphanumérique de 10 caractères.
            $folderId       =   $secureService->random_hash(5);
            // Chemin temporaire du fichier
            $fileBasePath  =   Constants::DYN_UPLOAD_PATH . $folderId . '/';
            // Nom du fichier
            $filename       =   $file->getClientOriginalName();

            // Déplacement du fichier dans le dossier temporaire
            if($file->move($fileBasePath, $filename)){

                // Si le fichier est un pdf
                $mimeType = new MimeTypes();
                if($mimeType->guessMimeType($fileBasePath . $filename) == 'application/pdf'){
                    // Nom de la vignette PDF
                    $thumb_filename =  pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
                    // On créer un vignette de la première page du pdf
                    $pdf = new Pdf($fileBasePath . $filename);
                    $pdf->saveImage($fileBasePath . $thumb_filename);
                    // Chemin de la vignette
                    $response["thumbUrl"] = $context->getRequest()->getBaseUrl() . $fileBasePath . $thumb_filename;
                }

                $response["folderId"]   =   $folderId;
                $response["filename"]   =   $filename;
                $file = new File($fileBasePath . $filename);
                $response["filesize"]   =   $file->getSize();
                // Chemin de l'image
                $response["url"]        =  $context->getRequest()->getBaseUrl() . $fileBasePath . $filename;
            }
        }else{
            $response["error"] = "Impossible de récupérer le fichier";
        }
        // Retour
        return new JsonResponse($response);
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
            // Chemin temporaire de l'image
            $folderPath = Constants::DYN_UPLOAD_PATH . $folderId . '/';
            $filesystem = new Filesystem();
            // Si le fichier existe.
            if ($filesystem->exists($folderPath)) {
                // Suppression du fichier.
                $filesystem->remove($folderPath);
            } else {
                $response["error"] = "Impossible de supprimer le fichier";
            }
        }

        // Récupération de l'id de l'entité
        $entityId = $context->getRequest()->get('entityId');
        if($entityId != null){
            $crudAction = Action::EDIT;
        }else{
            $crudAction = Action::NEW;
        }
        $url = $adminUrlGenerator->setAction($crudAction)
            ->set('entityId', $entityId)
            ->unset('folderId')
            ->generateUrl();
        // Retour
        return $this->redirect($url);
    }



    /**
     * @return array
     */
    protected function getMediaFields(): array
    {
        // Liste des champs à retourner.
        $mediaFields = array();
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
        // Si ils existent.
        if ($mediaspecs != null) {

            // MEDIAS
            // Ajout d'un onglet
            $mediaFields[] = FormField::addTab('Medias')
                ->setIcon('image');
            // Pour chaque mediaspec
            foreach ($mediaspecs as $index => $mediaspec) {
                $mediaFields[] =  FormField::addRow();

                // Ajout d'un champ d'upload d'un média
                // Ajout du personnalisé champ média.
                $imageField = Field::new('media' . ($index + 1), ucfirst($mediaspec->getName()) . ' : téléchargez un média ou...');
                $imageField->setColumns(8);
                // Récupération du média.
                $media = $this->entityManager->getRepository($this->getEntityFqcn())->getMedia($this->entity, $mediaspec);
                // Si l'entité possède un média pour cette mediaspec.
                if ($media != null) {
                    $imageField->setLabel(ucfirst($mediaspec->getName()))
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

}