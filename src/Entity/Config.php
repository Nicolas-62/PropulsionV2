<?php

namespace App\Entity;

use App\Repository\ConfigRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigRepository::class)]
class Config
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_contact = null;

    #[ORM\Column(length: 255)]
    private ?string $email_objet = null;

    #[ORM\Column]
    private ?int $cache_flush_auto = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmailContact(): ?string
    {
        return $this->email_contact;
    }

    public function setEmailContact(?string $email_contact): self
    {
        $this->email_contact = $email_contact;

        return $this;
    }

    public function getEmailObjet(): ?string
    {
        return $this->email_objet;
    }

    public function setEmailObjet(string $email_objet): self
    {
        $this->email_objet = $email_objet;

        return $this;
    }

    public function getCacheFlushAuto(): ?int
    {
        return $this->cache_flush_auto;
    }

    public function setCacheFlushAuto(int $cache_flush_auto): self
    {
        $this->cache_flush_auto = $cache_flush_auto;

        return $this;
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

  public function __toString(): string
  {
    return 'Configuration du site';
  }


}
