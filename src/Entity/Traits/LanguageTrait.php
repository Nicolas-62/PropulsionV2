<?php

  namespace App\Entity\Traits;

  trait LanguageTrait
  {

      /**
       * code langue : fr|en
       *
       * @var string|null
       */
      private ?string $language = null;


      /**
       * retourne le code langue : fr|en
       *
       * @return string|null
       */
    public function getLanguage(): ?string
    {

      return $this->language;
    }

    /**
     * défini le code langue : fr|en
     *
     * @param string $language
     */
    public function setLanguage($language): self
    {
      $this->language = $language;
      return $this;
    }
  }