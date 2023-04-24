<?php

namespace App\Notification;

use App\Constants\Constants;
use App\Entity\Contact;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class ContactNotification {

    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer, $locale){
        $this->mailer = $mailer;
        // TEST passage de variable à un service, voir : fichier services.yaml
        dump($locale);
    }

    public function notify(Contact $contact): void
    {
        $message = (new TemplatedEmail())
            ->subject("Message en provenance du site")
            ->replyTo($contact->getEmail())
            ->htmlTemplate('frontoffice/emails/contact.html.twig')
            ->context([
                'contact' => $contact
            ])
        ;
        $this->mailer->send($message);
    }
}