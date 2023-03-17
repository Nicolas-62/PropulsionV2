<?php
namespace App\EventListener;


use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Langues;
use App\Entity\Media;
use App\Entity\MediaLink;
use App\Entity\Online;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class CreationListener implements EventSubscriberInterface
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
            AfterEntityPersistedEvent::class => 'onAfterEntityPersisted',
            BeforeCrudActionEvent::class   => 'onBeforeCrudActionEvent',
            BeforeEntityUpdatedEvent::class => 'saveMedias'
        ];
    }


    /** BeforeEntityUpdatedEvent Permet de faire des actions avant la modification d'une entité
     *
     * @param BeforeEntityUpdatedEvent $event
     * @return void
     */
    public function saveMedias(BeforeEntityUpdatedEvent $event)
    {
        // Récupération de l'entité
        $entity = $event->getEntityInstance();
        // Si c'est un article ou une catégorie.
        if($entity instanceof  Article || $entity instanceof  Category){
            // Récupération des médiaspecs qui s'appliquent à l'entité
            $mediaspecs = $this->entityManager->getRepository($entity::class)->getMediaspecs($entity);
            // Pour chaque médiaspec
            foreach ($mediaspecs as $index => $mediaspec) {
                // Objet média
                $media = null;
                // Si un média a été envoyé.
                if($entity->{'getMedia'.$index+1}() != null){
                    // On crée le média
                    $media = new Media();
                    // On ajoute le fichier au média.
                    $media->setFile($entity->{'getMedia'.$index+1}());
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
    public function onBeforeCrudActionEvent(BeforeCrudActionEvent $event)
    {


        // Code permettant le switch online / offline des articles

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
                // Si on ajoute/enlève en ligne.
                if($fieldName == 'isOnline') {
                    // On récupère l'objet Online
                    $online = $entity->getOnlineByCodeLangue('fr');
                    // Si il n'existe pas.
                    if( ! $online){
                        $online = new Online();
                        $online->{'set'.ucfirst($entity->getClassName())}($entity);
                        $online->setLangue($this->entityManager->getRepository(Langues::class)->findOneByCode('fr'));
                    }
                    // On passe la valeur que l'on souhaite mettre à jour
                    $online->setOnline($newValue);
                    // On envoie l'objet à la BDD
                    $this->entityManager->persist($online);
                    $this->entityManager->flush();
                }
            }
        }

    }


    /** onAfterEntityPersisted écoute quand une entité est créée et permet de faire des actions
     *
     * @param AfterEntityPersistedEvent $event
     * @return void
     */
    public function onAfterEntityPersisted(AfterEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        // Ajouter une ligne à la table online lié à l'article et avec online = 0 pendant la création d'un article
        if ($entity instanceof Article) {
            $online = new Online();
            $online->setArticle($entity);
            $online->setCategory(null);
            $langue = $this->entityManager->getRepository(Langues::class)->find(1);
            $online->setLangue($langue);


            // On passe l'objet à la BDD
            $this->entityManager->persist($online);
            $this->entityManager->flush();
        }


        // Ajouter une ligne à la table online lié à la catégorie et avec online = 0 pendant la création d'un catégorie
        if ($entity instanceof Category) {
            $online = new Online();
            $online->setArticle(null);
            $online->setCategory($entity);
            $langue = $this->entityManager->getRepository(Langues::class)->getDefaultLangue();
            $online->setLangue($langue);


            // On passe l'objet à la BDD
            $this->entityManager->persist($online);
            $this->entityManager->flush();
        }
    }
}
