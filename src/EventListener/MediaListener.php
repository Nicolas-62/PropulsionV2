<?php
namespace App\EventListener;


use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Language;
use App\Entity\Media;
use App\Entity\MediaLink;
use App\Entity\Online;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;


class MediaListener implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /** getSubscribedEvents permet de mettre en place les listeners souhaités que l'on retrouve dans ce fichier
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeCrudActionEvent::class    => 'unlinkMedia',
            BeforeEntityUpdatedEvent::class => 'linkMedias',
            AfterEntityDeletedEvent::class  => 'removeMedia',
        ];
    }

    public function removeMedia(AfterEntityDeletedEvent $event){
        $entity = $event->getEntityInstance();
        // Si l'élément supprimé n'est pas un média
        if (!($entity instanceof Media)){
            return;
        }
        // Chargement composant Filesystem
        $filesystem = new Filesystem();

        // Chemin de l'image
        $imagepath = Constants::ASSETS_IMG_PATH.$entity->getMedia();
        // Suppression de l'image
        if($filesystem->exists($imagepath)) {
            $filesystem->remove($imagepath);
        }
    }




    /** saveMedias Permet de faire des actions avant la modification d'une entité
     *
     * @param BeforeEntityUpdatedEvent $event
     * @return void
     */
    public function linkMedias(BeforeEntityUpdatedEvent $event)
    {
        dump('BeforeEntityUpdatedEvent : linkMedias');
        // Récupération de l'entité
        $entity = $event->getEntityInstance();
        // Si c'est un article ou une catégorie.
        if($entity instanceof  Article || $entity instanceof  Category){
            // Récupération des mediaspecs qui s'appliquent à l'entité
            $mediaspecs = $this->entityManager->getRepository($entity::class)->getMediaspecs($entity);
            // Pour chaque mediaspec
            foreach ($mediaspecs as $index => $mediaspec) {
                // Objet média
                $media = null;
                // Si un média a été envoyé.
                if($entity->{'getMedia'.$index+1}() != null){
                    // On crée le média
                    $media = new Media();
                    // On ajoute le fichier au média.
                    $media->setMedia($entity->{'getMedia'.$index+1}());
                // Si un média existant a été choisi.
                }else if($entity->{'getMedia'.$index+11}() != null){
                    // On récupère le média.
                    $media = $this->entityManager->getRepository(Media::class)->findOneBy(['id' => $entity->{'getMedia'.$index+11}()]);
                }
                // Si un média a été renseigné
                if($media != null){
                    // On créer un lien entre...
                    $mediaLink = new MediaLink();
                    // la mediaspec...
                    $mediaLink->setMediaspec($mediaspec);
                    // l'entité...
                    $entity->addMediaLink($mediaLink);
                    // et le média.
                    $media->addMediaLink($mediaLink);
                    // On sauvegarde le média.
                    $this->entityManager->getRepository(Media::class)->save($media);
                    // On sauvegarde le lien.
                    $this->entityManager->getRepository(MediaLink::class)->save($mediaLink);
                    // On sauvegarde l'entité.
                    $this->entityManager->getRepository($entity::class)->save($entity);
                }
            }
        }
    }


    /** onBeforeCrudActionEvent Permet de faire des actions avant qu'une action crud agisse
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
