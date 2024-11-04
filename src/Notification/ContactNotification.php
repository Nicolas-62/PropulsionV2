<?php

namespace App\Notification;

use App\Constants\Constants;
use App\Entity\Contact;
use App\Repository\ConfigRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class ContactNotification {

    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer,  ParameterBagInterface $params, ConfigRepository $configRepository){
        $this->mailer = $mailer;
        $this->params = $params;
        $this->configRepsitory = $configRepository;
        // TEST passage de variable à un service, voir : fichier services.yaml
        //dump($locale);
    }

    /**
     * Construit et envoi le mail à partir des saisies du formulaire de contact.
     * @param Contact $contact
     * @return void
     * @throws TransportExceptionInterface
     */
    public function notify(Contact $contact): void
    {
        // Récupération de la config
        $config = $this->configRepsitory->getConfig();
        // Construction du message
        $message = (new TemplatedEmail())
            // Récupération du sujet depuis la config
            ->subject($config->getSubjectObjectById($contact->getSubject()));

            // Récupération des adresses mail en fonction du sujet, depuis la config
            foreach(explode(',', $config->getSubjectEmailById($contact->getSubject())) as $email){
                if(trim($email) != ''){
                    $message->addTo($email);
                }
            }
            // Sur la dev on envoie qu'à l'admin
            if($this->params->get('app.env') == 'dev'){
                $message->to($this->params->get('app.admin_email'));
            }else{
                // Sinon on met l'admin en copie cachée. (à désactiver..)
                //$message->addBcc($this->params->get('app.admin_email'));
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