<?php
namespace App\EventListener;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Language;
use App\Entity\Online;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class OnlineListener implements EventSubscriberInterface
{

    public function __construct(
        // Gestionnaire d'entité
        private EntityManagerInterface $entityManager,
        // Code Langue
        private string $locale
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
            BeforeCrudActionEvent::class     => 'editOnline',
            AfterEntityPersistedEvent::class => 'initOnline',
        ];
    }

    /** onBeforeCrudActionEvent Permet de faire des actions avant qu'une action crud agisse
     *
     * @param BeforeCrudActionEvent $event
     * @return void
     */
    public function editOnline(BeforeCrudActionEvent $event)
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
                // Si on ajoute/enlève en ligne.
                if($fieldName == 'isOnline') {
                    // On récupère l'objet Online de la langue courante.
                    $online = $entity->getOnlineByCodeLangue($this->locale);
                    // Si il n'existe pas.
                    if( ! $online){
                        $online = new Online();
                        $online->{'set'.ucfirst($entity->getClassName())}($entity);
                        $online->setLangue($this->entityManager->getRepository(Language::class)->findOneBy(['code' => $this->locale]));
                    }
                    // On passe la valeur que l'on souhaite mettre à jour
                    $online->setOnline($newValue);
                    // On envoie l'objet à la BDD
                    $this->entityManager->persist($online);
                    $this->entityManager->flush();
                // Si on supprime un média.
                }
            }
        }

    }


    /** onAfterEntityPersisted écoute quand une entité est créée et permet de faire des actions
     *
     * @param AfterEntityPersistedEvent $event
     * @return void
     */
    public function initOnline(AfterEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        // Ajouter une ligne à la table online lié à l'article et avec online = 0 pendant la création d'un article
        if ($entity instanceof Article) {
            $online = new Online();
            $online->setArticle($entity);
            $online->setCategory(null);

            $langue = $this->entityManager->getRepository(Language::class)->findOneBy(['code' => $this->locale]);
            $online->setLangue($langue);


            // On passe l'objet à la BDD
            $this->entityManager->persist($online);
            $this->entityManager->flush();
        }


        // Ajouter une ligne à la table online lié à la catégorie et avec online = 0 pendant la création d'une catégorie
        if ($entity instanceof Category) {
            $online = new Online();
            $online->setArticle(null);
            $online->setCategory($entity);
            $langue = $this->entityManager->getRepository(Language::class)->getDefaultLangue();
            $online->setLangue($langue);


            // On passe l'objet à la BDD
            $this->entityManager->persist($online);
            $this->entityManager->flush();
        }
    }
}
