<?php

namespace App\Controller\Backoffice;

use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\Media;
use App\Field\MediaSelectField;
use App\Service\Secure;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use http\Client\Response;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class BoController extends AbstractCrudController
{
    public function __construct(
    )
    {
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
        );

        // Récupération de l'image
        $file = $context->getRequest()->files->get('file');

        // DEBUG
        //dump($file->isValid());

        // Si l'image a été récupérée.
        if(isset($file) && $file->isValid()) {
            // Dossier temporaire de l'image = chaine alphanumérique de 10 caractères.
            $folderId = $secureService->random_hash(5);
            // Chemin temporaire de l'image
            $imageBasePath = Constants::ASSETS_UPLOAD_PATH . $folderId . '/';
            // Déplacement de l'image dans le dossier temporaire
            if($file->move($imageBasePath, $file->getClientOriginalName())){
                $response["folderId"] = $folderId;
                $response["filename"] = $file->getClientOriginalName();
                // Transformation de l'image en base64
                $response["url"]      =  $context->getRequest()->getBaseUrl() . $imageBasePath . $file->getClientOriginalName();
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
        // Chemin temporaire de l'image
        $imageBasePath = Constants::ASSETS_UPLOAD_PATH . $folderId . '/';
        $filesystem = new Filesystem();
        $filepath = $imageBasePath . $filename;
        // Si le fichier existe.
        if($filesystem->exists($filepath)) {
            // Suppression du fichier.
            $filesystem->remove($filepath);
        }else{
            $response["error"] = "Impossible de supprimer le fichier";
        }

        // Récupération de l'id de l'entité
        $entityId = $context->getRequest()->get('entityId');
        if($entityId != null){
            $crudAction = Action::EDIT;
        }else{
            $crudAction = Action::NEW;
        }
        $url = $adminUrlGenerator->setAction($crudAction)
            ->set('entityId', $context->getRequest()->get('entityId'))
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




}