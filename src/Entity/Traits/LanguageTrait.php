<?php

  namespace App\Entity\Traits;

  use Doctrine\ORM\Mapping as ORM;

  trait LanguageTrait
  {

    private $language;


    public function __construct()
    {

    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
      return $this->language;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language): void
    {
      $this->language = $language;
    }
  }