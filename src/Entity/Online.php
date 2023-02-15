<?php

namespace App\Entity;

use App\Repository\OnlinesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OnlinesRepository::class)]
class Online
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist'],inversedBy: 'online')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Langues $langue = null;

    #[ORM\Column]
    private ?bool $online = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_modification = null;

    #[ORM\ManyToOne(cascade: ['remove'],inversedBy: 'online')]
    private ?Article $article = null;

    #[ORM\ManyToOne(cascade: ['remove'],inversedBy: 'online')]
    private ?Category $category = null;

    public function __construct()
    {
        $this->article = new Article();
        $this->category = new Category();
        $this->date_creation = new \DateTimeImmutable();
        $this->date_modification = new \DateTimeImmutable();
        $this->online = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getLangue(): ?Langues
    {
        return $this->langue;
    }

    public function setLangue(?Langues $langue): self
    {
        $this->langue = $langue;

        return $this;
    }

    public function isOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool|string $online): self
    {
        if($online == 'false'){
            $this->online = false;
        }
        else if($online == 'true'){
            $this->online = true;
        }else {
            $this->online = $online;
        }

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
}
