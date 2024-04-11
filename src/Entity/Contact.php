<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;


class Contact {

    const SUBJECTS = [
        0 => ['index' => 0, 'label' => 'Billetterie', 'variable' => 'billetterie'],
        1 => ['index' => 1, 'label' => 'Comptabilité', 'variable' => 'comptabilite'],
        2 => ['index' => 2, 'label' => 'Partenariats', 'variable' => 'partenariats'],
        3 => ['index' => 3, 'label' => 'Communication', 'variable' => 'communication'],
        4 => ['index' => 4, 'label' => 'Projets', 'variable' => 'projets' ],
        5 => ['index' => 5, 'label' => 'Programmation', 'variable' => 'programmation'],
        6 => ['index' => 6, 'label' => 'Technique', 'variable' => 'technique'],
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

    #[Assert\NotBlank]
    private int $subject;


    private ?string $replyTo = null;

    private string $templatePath = 'frontoffice/emails/contact.html.twig';

    /**
     * @var bool|null
     */
    private ?bool $getNewsletter;


    public function __construct(){
        // Email du client par défaut.
        $this->email = $_ENV['CLIENT_EMAIL'];
        // Message par défaut
        $this->subject = 0;
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

    public function getSubject(): int
    {
        return $this->subject;
    }

    public function setSubject(int $subject): void
    {
        $this->subject = $subject;
    }

}