<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


class Contact {

    const SUBJECTS = [
        0 => 'Sujet 1',
        1 => 'Sujet 2',
        2 => 'Sujet 3',
        3 => 'Sujet 4'
    ];

    /**
     * @var string|null
     */
    #[Assert\Length(
        min: 2,
        max: 50,
//        minMessage: 'Your first name must be at least {{ limit }} characters long',
//        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    #[Assert\NotBlank]
    private $name;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Email()]
    private $email;

    /**
     * @var string|null
     */
    #[Assert\Length(
        min: 10,
    )]
    #[Assert\NotBlank]
    private $message;

    #[Assert\Values(
        values: Contact::SUBJECTS,
        message: 'Veuillez choisir un sujet valide'
    )]
    #[Assert\NotBlank]
    private $subject;

    /**
     * @var bool|null
     */
    private $getNewsletter;

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return Contact
     */
    public function setEmail(?string $email): Contact
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
     * @param mixed $subject
     */
    public function setSubject($subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
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