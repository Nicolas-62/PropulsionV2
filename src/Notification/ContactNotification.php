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
        //dump($locale);
    }

    public function notify(Contact $contact): void
    {
        $message = (new TemplatedEmail())
            ->subject($contact->getSubject());
            // Si c'est une liste de mails.
            if(is_array($contact->getEmail())){
                foreach($contact->getEmail() as $email){
                    $message->addTo($email);
                }
            }else{
                $message->to($contact->getEmail());
            }
            $message
            ->replyTo($contact->getReplyTo())
            ->htmlTemplate($contact->getTemplatePath())
            ->context([
                'contact' => $contact
            ])
        ;
        $this->mailer->send($message);
    }
}