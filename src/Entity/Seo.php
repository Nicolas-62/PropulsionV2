<?php

namespace App\Entity;

use App\Repository\SeoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeoRepository::class)]
class Seo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'seos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Language $langue = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'seo')]
    private ?Article $article = null;

    #[ORM\ManyToOne(inversedBy: 'Seo')]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLangue(): ?Language
    {
        return $this->langue;
    }

    public function setLangue(?Language $langue): self
    {
        $this->langue = $langue;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
    return $this->getTitle();
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
