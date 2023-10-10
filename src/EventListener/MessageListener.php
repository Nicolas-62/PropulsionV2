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
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Notifier\Event\SentMessageEvent;


class MessageListener implements EventSubscriberInterface
{

    public function __construct(

    )
    {
    }

    public static function getSubscribedEvents()
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            SentMessageEvent::class    => 'onMessage',
        ];
    }

    /**
     * En cours de développement, ne fonctionne pas pour l'instant
     * https://symfony.com/doc/current/mailer.html#sentmessageevent
     *
     * @param SentMessageEvent $event
     * @return void
     */
    public function onMessage(SentMessageEvent $event): void
    {
        $message = $event->getMessage();
        if (!$message instanceof SentMessage) {
            return;
        }
//        else{
//            $message->getDebug();
//        }
    }


}
