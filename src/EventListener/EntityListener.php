<?php

namespace App\EventListener;

use App\Entity\Article;
use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatableMessage;

class EntityListener implements EventSubscriberInterface
{
    private SessionInterface $session;
    private AdminContextProvider $context;

    public function __construct(AdminContextProvider $contextProvider, RequestStack $request)
    {
        $this->context = $contextProvider;
        $this->session = $request->getSession();
    }

    /**
     * permet de mettre en place les listeners souhaités que l'on retrouve dans ce fichier
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityUpdatedEvent::class => 'defineParent',
            BeforeEntityPersistedEvent::class => 'defineParent',
        ];

    }

    /**
     * Controle le choix d'un article parent ou d'une catégorie
     *
     * @param BeforeEntityUpdatedEvent|BeforeEntityPersistedEvent $event
     * @return void
     */
    public function defineParent(BeforeEntityUpdatedEvent|BeforeEntityPersistedEvent $event)
    {
        // Récupération de l'entité.
        $entity = $event->getEntityInstance();

        // Si l'entité est une catégorie ou un article.
        if ($entity instanceof Article) {
            // Temoin choix valide d'un parent.
            $validChoice  = true;
            // Si l'entité a un parent.
            if ($entity->getParent() != null) {
                // Si l'entité a une catégorie.
                if($entity->getCategory() != null){
                    $validChoice = false;
                }
            }
            // Si l'entité ne possède pas de parent.
            else{
                // Si l'entité n'a pas de catégorie.
                if($entity->getCategory() == null){
                    $validChoice = false;
                }
            }
            // Si le choix n'est pas valide.
            if(!$validChoice) {

                // Les messages flash ne sont pas générés pour les requêtes AJAX
                if (!$this->context->getContext()->getRequest()->isXmlHttpRequest()) {
                    $this->session->getFlashBag()->add('danger', new TranslatableMessage('content_admin.flash_message.error.parent.choice', [
                        '%name%' => (string)$event->getEntityInstance(),
                    ], 'messages'));
                }
            }
        }
    }




}