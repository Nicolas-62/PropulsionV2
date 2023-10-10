<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;


class Contact {

    const SUBJECTS = [
            0 => ['label' => 'Billetterie', 'email' => 'marie.yachkouri@lalune.net'],
            1 => ['label' => 'Comptabilité', 'email' => 'sandrine.darlot@lalune.net'],
            2 => ['label' => 'Partenariats', 'email' => 'antoine.grillon@lalune.net'],
            3 => ['label' => 'Presse et communication', 'email' => 'jimmy.bourbier@lalune.net'],
            4 => ['label' => 'Projets scolaires, ateliers, actions culturelles', 'email' => ['anais.frapsauce@lalune.net', 'marine.salvat@lalune.net'] ],
            5 => ['label' => 'Programmation', 'email' => 'antoine.grillon@lalune.net'],
            6 => ['label' => 'Technique', 'email' => 'antoine.breny@lalune.net'],
    ];

    /**
     * @var string
     */
    #[Assert\Length(
        min: 2,
        max: 50,
//        minMessage: 'Your first name must be at least {{ limit }} characters long',
//        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    #[Assert\Email()]
    private array|string $email;

    /**
     * @var string|null
     */
    #[Assert\Length(
        min: 10,
    )]
    #[Assert\NotBlank]
    private ?string $message;

    #[Assert\Choice(
        callback: 'getSubjects',
        message: 'Veuillez choisir un sujet valide'
    )]
    #[Assert\NotBlank]
    private string $subject;


    private ?string $replyTo = null;

    private string $templatePath = 'frontoffice/emails/contact.html.twig';

    /**
     * @var bool|null
     */
    private ?bool $getNewsletter;


    public function __construct(){
        // Email du client par défaut.
        $this->email = $_ENV['CLIENT_EMAIL'];
        // MEssage par défaut
        $this->subject = 'Message en provenance du site - '.$_ENV['SITE'];
    }


    public function getReplyTo(): ?string
    {
        return $this->replyTo;
    }

    public function setReplyTo(string $replyTo): Contact
    {
        $this->replyTo = $replyTo;
        return $this;
    }

    public function getTemplatePath(): ?string
    {
        return $this->templatePath;
    }

    public function setTemplatePath(?string $templatePath): Contact
    {
        $this->templatePath = $templatePath;
        return $this;
    }


    public function getEmail(): string|array
    {
        return $this->email;
    }

    /**
     * @return Contact
     */
    public function setEmail(string|array $email): Contact
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     * @return Contact
     */
    public function setMessage(?string $message): Contact
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }


    /**
     * Retourne le label des sujets
     *
     * @return array
     */
    public static function getSubjects()
    {
        $subjects = array();
        foreach(Contact::SUBJECTS as $subject){
            $subjects[] = $subject['label'];
        }
        return $subjects;
    }

    /**
     * Retourne l'adresse mail en fonction du sujet passé en parametre
     * @param string $subject_send
     * @return string|array|false
     */
    public static function getEmailBySubjectLabel(string $subject_send): string|array|false
    {
        foreach(Contact::SUBJECTS as $subject){
            if($subject['label'] == $subject_send){
                return $subject['email'];
            }
        }
        return false;
    }



    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string|null
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool|null
     */
    public function getGetNewsletter(): ?bool
    {
        return $this->getNewsletter;
    }

    /**
     * @param bool|null $getNewsletter
     */
    public function setGetNewsletter(?bool $getNewsletter): void
    {
        $this->getNewsletter = $getNewsletter;
    }

}