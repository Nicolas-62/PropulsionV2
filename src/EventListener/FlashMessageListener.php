<?php
namespace App\EventListener;


use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatableMessage;


class FlashMessageListener implements EventSubscriberInterface
{
    private SessionInterface $session;
    private AdminContextProvider $context;

    public function __construct(AdminContextProvider $contextProvider, RequestStack $request)
    {
        $this->context = $contextProvider;
        $this->session = $request->getSession();
    }


    /** getSubscribedEvents permet de mettre en place les listeners souhaités que l'on retrouve dans ce fichier
     *
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityPersistedEvent::class => 'flashMessageAfterPersist',
            AfterEntityUpdatedEvent::class => 'flashMessageAfterUpdate',
            AfterEntityDeletedEvent::class => 'flashMessageAfterDelete'
        ];

    }

    public function flashMessageAfterPersist(AfterEntityPersistedEvent $event): void
    {
        // Les messages flash ne sont pas générés pour les requêtes AJAX
        if( ! $this->context->getContext()->getRequest()->isXmlHttpRequest()) {
            $this->session->getFlashBag()->add('success', new TranslatableMessage('content_admin.flash_message.create', [
                '%name%' => (string)$event->getEntityInstance(),
            ], 'messages'));
        }
    }

    public function flashMessageAfterUpdate(AfterEntityUpdatedEvent $event): void
    {
        if( ! $this->context->getContext()->getRequest()->isXmlHttpRequest()) {
            $this->session->getFlashBag()->add('success', new TranslatableMessage('content_admin.flash_message.update', [
                '%name%' => (string)$event->getEntityInstance(),
            ], 'messages'));
        }
    }

    public function flashMessageAfterDelete(AfterEntityDeletedEvent $event): void
    {
        if( ! $this->context->getContext()->getRequest()->isXmlHttpRequest()) {
            $this->session->getFlashBag()->add('success', new TranslatableMessage('content_admin.flash_message.delete', [
                '%name%' => (string)$event->getEntityInstance(),
            ], 'messages'));
        }
    }
}