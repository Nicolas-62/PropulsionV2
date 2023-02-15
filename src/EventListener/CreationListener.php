<?php
namespace App\EventListener;


use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Langues;
use App\Entity\Online;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
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
        ];
    }


    /** BeforeEntityUpdatedEvent Permet de faire des actions avant la modification d'une entité
     *
     * @param BeforeEntityUpdatedEvent $event
     * @return void
     */
    public function onBeforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();
        dump($entity);


    }


    /** onBeforeCrudActionEvent Permet de faire des actions avant qu'une action crud agisse
     *
     * @param BeforeCrudActionEvent $event
     * @return void
     */
    public function onBeforeCrudActionEvent(BeforeCrudActionEvent $event)
    {


        // Code permettant le switch online / offline des articles
        $entity = $event->getAdminContext()->getEntity()->getInstance();
        $action = $event->getAdminContext()->getCrud()->getCurrentAction();
        $fieldName = $event->getAdminContext()->getRequest()->get("fieldName");
        $newValue = $event->getAdminContext()->getRequest()->get("newValue");

        if($action == "edit"){
            if($entity instanceof  Article || $entity instanceof  Category){
                // On récupère l'objet Online à récupérer
                $online = $entity->getOnlineByLangue();
                if($online && $fieldName == 'isOnline') {
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

       //dd($entity);

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
