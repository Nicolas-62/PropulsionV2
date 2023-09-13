<?php

namespace App\Notification;

use App\Constants\Constants;
use App\Entity\Contact;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Router;

class BoNotification {

    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer, ParameterBagInterface $params){
        $this->mailer = $mailer;
        $this->params = $params;
    }

    /**
     * Envoi un mail pour définir le mot de passe de l'utilisateur.
     *
     * @param User $user
     * @param $link_to_define_password
     * @return bool
     */
    public function sendAcces(User $user, $link_to_define_password): bool
    {
        // Création du mail
        $message = (new TemplatedEmail())
            ->subject("Accès au backoffice de ".$this->params->get('app.site'))
            ->replyTo($this->params->get('app.admin_email'))
            ->htmlTemplate('frontoffice/emails/password.html.twig')
            // Variables passées dans le contenu du mail.
            ->context([
                'user' => $user,
                'site_name' => $this->params->get('app.site'),
                'link'      => $link_to_define_password,
            ])
        ;
        // Flag mail envoyé
        $sent = true;
        try{
            $this->mailer->send($message);
        }catch(TransportExceptionInterface $exception){
            $sent = false;
        } finally {
            return $sent;
        }
    }
}