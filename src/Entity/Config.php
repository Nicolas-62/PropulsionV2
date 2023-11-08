<?php

namespace App\Entity;

use App\Entity\Traits\ErrorTrait;
use App\Repository\ConfigRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigRepository::class)]
class Config
{
    use ErrorTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?int $tauxTVA = null;


    public function __construct()
    {
        $this->created_at       = new \DateTimeImmutable();
        $this->updated_at       = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Methode nessecaire pour appel des elements dans un sélecteur de formulaire.
     *
     * @return string
     */
      public function __toString(): string
      {
          return 'Configuration du site';
      }

      public function getTauxTVA(): ?int
      {
          return $this->tauxTVA;
      }

      public function setTauxTVA(?int $tauxTVA): self
      {
          $this->tauxTVA = $tauxTVA;

          return $this;
      }

}
