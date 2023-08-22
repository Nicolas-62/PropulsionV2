<?php

namespace App\Entity;

use App\Entity\Traits\TimesTampableTrait;
use App\Repository\MediaspecsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaspecsRepository::class)]
class Mediaspec
{

    // Champs date.
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $width = null;

    #[ORM\Column]
    private ?int $height = null;

    #[ORM\Column]
    private ?bool $mandatory = null;

    #[ORM\Column]
    private ?bool $haslegend = null;

    #[ORM\Column]
    private ?array $heritage = array('0');

    #[ORM\OneToMany(mappedBy: 'mediaspec', targetEntity: MediaLink::class, cascade: ['remove'])]
    private Collection $mediaLinks;

    #[ORM\ManyToOne(inversedBy: 'mediaspecs')]
    private ?Article $article = null;

    #[ORM\ManyToOne(inversedBy: 'mediaspecs')]
    private ?Category $category = null;

    #[ORM\ManyToOne]
    private ?MediaType $mediaType = null;

    public function __construct()
    {
        $this->mediaLinks = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        if($this->name) {
            return $this->name;
        }else{
            return '';
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    /**
     * @return bool|null
     */
    public function getMandatory(): ?bool
    {
        return $this->mandatory;
    }

    /**
     * @param bool|null $mandatory
     */
    public function setMandatory(?bool $mandatory): void
    {
        $this->mandatory = $mandatory;
    }

    /**
     * @return Collection<int, MediaLink>
     */
    public function getMediaLinks(): Collection
    {
        return $this->mediaLinks;
    }

    public function addMediaLink(MediaLink $mediaLink): self
    {
        if (!$this->mediaLinks->contains($mediaLink)) {
            $this->mediaLinks->add($mediaLink);
            $mediaLink->setMediaspec($this);
        }

        return $this;
    }

    public function removeMediaLink(MediaLink $mediaLink): self
    {
        if ($this->mediaLinks->removeElement($mediaLink)) {
            // set the owning side to null (unless already changed)
            if ($mediaLink->getMediaspec() === $this) {
                $mediaLink->setMediaspec(null);
            }
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHaslegend(): ?bool
    {
        return $this->haslegend;
    }

    /**
     * @param bool|null $haslegend
     */
    public function setHaslegend(?bool $haslegend): void
    {
        $this->haslegend = $haslegend;
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

    public function getMediaType(): ?MediaType
    {
        return $this->mediaType;
    }

    public function setMediaType(?MediaType $mediaType): self
    {
        $this->mediaType = $mediaType;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getHeritage(): ?array
    {
        return $this->heritage;
    }

    /**
     * @param array|null $heritage
     */
    public function setHeritage(?array $heritage): void
    {
        $this->heritage = $heritage;
    }

}
