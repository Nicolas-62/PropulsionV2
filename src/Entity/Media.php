<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[CustomAssert\OneFilled]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MediasTypes $media_type_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $legende = null;

    #[ORM\Column(length: 255)]
    private ?string $fichier = null;


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_modification = null;

    /**
     * @Assert\NotNull(groups={"article_filled"})
     */
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Article', inversedBy: "media")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Article $article = null;

    /**
     * @Assert\NotNull(groups={"category_filled"})
     */
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Category', inversedBy: "media")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $category = null;

    /**
     * @Assert\Expression(
     *     "this.getCategory() != null xor this.getArticle() != null",
     *     message="Category ou Article doit être remplit, l'autre doit être null.",
     *     groups={"category_filled", "article_filled"}
     * )
     */
    public function isOneFilled()
    {
        return true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getMediaTypeId(): ?MediasTypes
    {
        return $this->media_type_id;
    }

    public function setMediaTypeId(?MediasTypes $media_type_id): self
    {
        $this->media_type_id = $media_type_id;

        return $this;
    }

    public function getLegende(): ?string
    {
        return $this->legende;
    }

    public function setLegende(?string $legende): self
    {
        $this->legende = $legende;

        return $this;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(string $fichier): self
    {
        $this->fichier = $fichier;

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

    public function __toString(): string
    {
        return $this->legende;
    }


}
