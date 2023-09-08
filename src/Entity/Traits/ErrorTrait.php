<?php

namespace App\Entity\Traits;

trait ErrorTrait
{

    // Flag d'erreur pour prévenir la sauvegarde de l'entite et de ses éléments liés (ex : online)
    private bool $error = false;


    public function hasError(): bool
    {
        return $this->error;
    }

    public function setError(bool $error = true): self
    {
        $this->error = $error;
        return $this;
    }

}