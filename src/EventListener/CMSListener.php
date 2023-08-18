<?php

namespace App\EventListener;

use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CMSListener implements EventSubscriberInterface
{
    /**
     * permet de mettre en place les listeners souhaités que l'on retrouve dans ce fichier
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityUpdatedEvent::class => 'update',
        ];

    }

    /**
     * Met à jour des élément lors de la modification des entités
     *
     * @param BeforeEntityUpdatedEvent $event
     * @return void
     */
    public function update(BeforeEntityUpdatedEvent $event)
    {
        // Récupération de l'entité.
        $entity = $event->getEntityInstance();

        if(method_exists($entity, 'setUpdatedAt')){
            $entity->setUpdatedAt(new \DateTimeImmutable());
        }
    }

}