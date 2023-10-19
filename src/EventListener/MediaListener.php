<?php
namespace App\EventListener;


use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Language;
use App\Entity\Media;
use App\Entity\MediaLink;
use App\Entity\MediaType;
use App\Entity\Online;
use App\Service\MediaService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;


class MediaListener implements EventSubscriberInterface
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
        // Gestionnaire d'entité Symfony
        private MediaService $mediaService
    )
    {
    }


    /** getSubscribedEvents permet de mettre en place les listeners souhaités que l'on retrouve dans ce fichier
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeCrudActionEvent::class    => 'unlinkMedia',
            AfterEntityUpdatedEvent::class => 'linkMedias',
            BeforeEntityDeletedEvent::class  => 'removeMedia',
            BeforeEntityPersistedEvent::class => 'createMedia'
        ];
    }

    public function createMedia(BeforeEntityPersistedEvent $event){

        $entity = $event->getEntityInstance();
        $new_filename = false;
        // Si l'élément créé est un média
        if ($entity instanceof Media) {
            $new_filename = $this->mediaService->getFile(
              $this->requestStack->getCurrentRequest()->get('folderId-media'),
              $this->requestStack->getCurrentRequest()->get('filename-media'),
              $this->requestStack->getCurrentRequest()->get('cropData-media')
            );
        }

        // Si un fichier a été déposé a été retrouvé sur le serveur
        if ($new_filename !== false) {
            // On associe l'image téléchargée à l'objet média en cours de création.
            $entity->setMedia( $new_filename );
            $entity->setMediaType( $this->entityManager->getRepository(MediaType::class)->findOneByFiletype($new_filename) );
        }
    }

    /**
     * Supprime le fichier avant la suppression de l'entité média
     *
     * @param BeforeEntityDeletedEvent $event
     * @return void
     */
    public function removeMedia(BeforeEntityDeletedEvent $event){
        $entity = $event->getEntityInstance();
        // Si l'élément supprimé n'est pas un média
        if (!($entity instanceof Media)){
            return;
        }else {
            // Si le media est défini et non null
            if($entity->getMedia() != null && trim($entity->getMedia()) != '') {
                // Chargement composant Filesystem
                $filesystem = new Filesystem();
                // Chemin de l'image
                $imagepath = Constants::DYN_IMG_PATH . $entity->getMedia();
                // Suppression de l'image
                if ($filesystem->exists($imagepath)) {
                    $filesystem->remove($imagepath);
                }
                // Chemin de la vignette
                $thumbpath = Constants::DYN_IMG_PATH . $entity->getThumbnail();
                // Suppression de la vignette
                if ($filesystem->exists($thumbpath)) {
                    $filesystem->remove($thumbpath);
                }
            }
        }
    }




    /**
     * Créer et/ou associe un média à un article/une catégorie
     *
     * @param AfterEntityUpdatedEvent $event
     * @return void
     */
    public function linkMedias(AfterEntityUpdatedEvent $event)
    {
        // dump('AfterEntityUpdatedEvent : linkMedias');
        // Récupération de l'entité
        $entity = $event->getEntityInstance();

        // Si c'est un article ou une catégorie.
        if($entity instanceof  Article || $entity instanceof  Category){
            // Si l'entité n'est pas en erreur.
            if( ! $entity->hasError()) {
                // SI c'est un article de la galerie
                if($entity instanceof  Article && $entity->getCategory()?->getId() == $_ENV['GALLERY_CATEGORY_ID']){
                    $new_filenames = $this->mediaService->getFiles(
                        $this->requestStack->getCurrentRequest()->get('folderId-galleryMediaUploads'),  'gallery/'
                    );
                    foreach ($new_filenames as $new_filename) {
                        if ($new_filename) {
                            $media = new Media();
                            // On associe l'image téléchargée à l'objet média en cours de création.
                            $media->setMedia($new_filename);
                            $media->setMediaType($this->entityManager->getRepository(MediaType::class)->findOneByFiletype($new_filename));
                            // On précise la section pour na pas les voir dans la phototheque.
                            $media->setSection('galerie');
                            // Todo controler la saisie de la description de l'image
                            $this->entityManager->persist($media);
                        }
                        // Si un média a été renseigné
                        if (isset($media)) {
                            // On créer un lien entre la mediaspec le media et la publication
                            $mediaLink = new MediaLink();
                            $mediaLink->setMedia($media)->{'set' . ucfirst($entity->getClassName())}($entity);
                            // On sauvegarde
                            $this->entityManager->getRepository(MediaLink::class)->save($mediaLink, true);
                        }
                    }
                }

                // !! FICHIERS
                // Suppression des anciens liens avec les fichiers
                $this->entityManager->getRepository(MediaLink::class)->removeFileMediaLinks($entity);
                // récupération des ids des medias à associer avec l'entité
                $mediaLinkIds = $this->requestStack->getCurrentRequest()->get(ucfirst($entity->getClassName()))['mediaLinks'] ?? [];
                // Pour chaque média à associer
                foreach($mediaLinkIds as $mediaLinkId){
                    // Création du lien
                    $mediaLink = new MediaLink();
                    // Récupération du média et association
                    $mediaLink->setMedia($this->entityManager->getRepository(Media::class)->findOneBy(['id' => $mediaLinkId]));
                    // Association du lien à l'entité
                    $entity->addMediaLink($mediaLink);
                    // Association de l'entité au lien
                    $mediaLink->{'set' . ucfirst($entity->getClassName())}($entity);
                    // Sauvegarde du lien
                    $this->entityManager->getRepository(MediaLink::class)->save($mediaLink);
                }

                // !! IMAGES
                // Récupération des mediaspecs qui s'appliquent à l'entité
                $mediaspecs = $this->entityManager->getRepository($entity::class)->getMediaspecs($entity);
                // Pour chaque mediaspec
                foreach ($mediaspecs as $index => $mediaspec) {

                    $new_filename = $this->mediaService->getFile(
                        $this->requestStack->getCurrentRequest()->get('folderId-media' . $index + 1),
                        $this->requestStack->getCurrentRequest()->get('filename-media' . $index + 1),
                        $this->requestStack->getCurrentRequest()->get('cropData-media' . $index + 1),
                        null,
                        true
                    );
                    // Variable qui va récupérer le média.
                    $media = null;
                    // Si un fichier a été déposé a été retrouvé sur le serveur
                    if ($new_filename != false) {
                        $media = new Media();
                        // On associe l'image téléchargée à l'objet média en cours de création.
                        $media->setMedia($new_filename);
                        $media->setMediaType( $this->entityManager->getRepository(MediaType::class)->findOneByFiletype($new_filename) );

                        // Todo controler la saisie de la description de l'image
                        $media->setLegend($this->requestStack->getCurrentRequest()->get('legend-media' . $index + 1));
                        $this->entityManager->persist($media);
                    } else {
                        // Si pas de fichier déposé mais un média existant sélectionné.
                        if ($entity->{'getMedia' . $index + 11}() != null) {
                            // On récupère le média.
                            $media = $this->entityManager->getRepository(Media::class)->findOneBy(['id' => $entity->{'getMedia' . $index + 11}()]);

                        }
                    }
                    // Si un média a été renseigné
                    if ($media != null) {
                        // On créer un lien entre la mediaspec le media et la publication
                        $mediaLink = new MediaLink();
                        $mediaLink->setMediaspec($mediaspec)->setMedia($media)->{'set' . ucfirst($entity->getClassName())}($entity);
                        // On sauvegarde
                        $this->entityManager->getRepository(MediaLink::class)->save($mediaLink, true);
                    }
                }
            }
        }


        // Mise à jour de la base de donnée
        $this->entityManager->flush();
    }


    /**
     * Permet de faire des actions avant qu'une action crud agisse
     *
     * @param BeforeCrudActionEvent $event
     * @return void
     */
    public function unlinkMedia(BeforeCrudActionEvent $event)
    {
        // Entité
        $entity = $event->getAdminContext()->getEntity()->getInstance();
        // Action en cours
        $action = $event->getAdminContext()->getCrud()->getCurrentAction();
        // Nom du champ en cours d'édition.
        $fieldName = $event->getAdminContext()->getRequest()->get("fieldName");
        // Valeur
        $newValue = $event->getAdminContext()->getRequest()->get("newValue");

        // Si on en édition
        if($action == "edit"){
            // Si c'est un article ou une catégorie
            if($entity instanceof  Article || $entity instanceof  Category){
                // Si on supprime le lien avec le média.
                if($fieldName == 'unlink_media'){
                    // Récupération du lien avec le média.
                    $media_link = $this->entityManager->getRepository(MediaLink::class)->findOneBy(['media' => $newValue]);
                    // Si le lien existe.
                    if($media_link != null) {
                        $entity->removeMediaLink($media_link);
                        $this->entityManager->getRepository(MediaLink::class)->remove($media_link);
                        $this->entityManager->flush();
                    }
                }
            }
        }
    }
}
