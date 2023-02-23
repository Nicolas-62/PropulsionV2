<?php

namespace App\Entity;

use App\Entity\Traits\TimesTampableTrait;
use App\Repository\MediaLinkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaLinkRepository::class)]
class MediaLink
{

    // Champs date.
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'mediaLinks')]
    private ?Mediaspec $mediaspec = null;

    #[ORM\ManyToOne(inversedBy: 'mediaLinks')]
    private ?Article $article = null;

    #[ORM\ManyToOne(inversedBy: 'mediaLinks')]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'mediaLinks')]
    private ?Media $media = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMediaspec(): ?Mediaspec
    {
        return $this->mediaspec;
    }

    public function setMediaspec(?Mediaspec $mediaspec): self
    {
        $this->mediaspec = $mediaspec;

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

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        return $this;
    }
}
