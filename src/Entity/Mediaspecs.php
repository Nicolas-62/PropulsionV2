<?php

namespace App\Entity;

use App\Repository\MediaspecsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaspecsRepository::class)]
class Mediaspecs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $objet = null;


    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $width = null;

    #[ORM\Column]
    private ?int $height = null;

    #[ORM\Column]
    private ?bool $is_mandatory = null;

    #[ORM\Column]
    private ?bool $haslegende = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_modification = null;

    #[ORM\ManyToOne]
    private ?Article $article = null;

    #[ORM\ManyToOne(inversedBy: 'mediaspecs')]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'mediaspecs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MediasTypes $mediaType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): self
    {
        $this->objet = $objet;

        return $this;
    }


    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function isIsMandatory(): ?bool
    {
        return $this->is_mandatory;
    }

    public function setIsMandatory(bool $is_mandatory): self
    {
        $this->is_mandatory = $is_mandatory;

        return $this;
    }

    public function isHaslegende(): ?bool
    {
        return $this->haslegende;
    }

    public function setHaslegende(bool $haslegende): self
    {
        $this->haslegende = $haslegende;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->date_modification;
    }

    public function setDateModification(\DateTimeInterface $date_modification): self
    {
        $this->date_modification = $date_modification;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getMediaType(): ?MediasTypes
    {
        return $this->mediaType;
    }

    public function setMediaType(?MediasTypes $mediaType): self
    {
        $this->mediaType = $mediaType;

        return $this;
    }
}
