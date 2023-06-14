<?php

namespace App\Entity;

use App\Entity\Traits\TimesTampableTrait;
use App\Repository\MediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
// TEST VICHUPLOAD BUNDLE
//#[Vich\Uploadable]
class Media
{
    // Champs date.
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $media = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $legend = null;

    #[ORM\OneToMany(mappedBy: 'media', targetEntity: MediaLink::class, cascade: ['remove'])]
    private Collection $mediaLinks;

// TEST VICHUPLOAD BUNDLE
//    #[Assert\Image(mimeTypes: ['image/jpeg'])]
//    #[Vich\UploadableField(mapping: 'cms_media', fileNameProperty: 'media')]
//    private ?File $mediaFile = null;

    public function __construct()
    {
        $this->mediaLinks = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getLegend(): ?string
    {
        return $this->legend;
    }

    public function setLegend(?string $legend): self
    {
        $this->legend = $legend;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getMediaFile(): ?File
    {
        return $this->mediaFile;
    }

    /**
     * @param File|null $mediaFile
     */
    public function setMediaFile(File $mediaFile = null): self
    {
        $this->mediaFile = $mediaFile;
        if (null !== $mediaFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated_at = new \DateTimeImmutable();
        }
        return $this;
    }

    public function __toString(): string
    {
        if($this->legend) {
            return $this->legend;
        }else{
            return '';
        }
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
            $mediaLink->setMedia($this);
        }

        return $this;
    }

    public function removeMediaLink(MediaLink $mediaLink): self
    {
        if ($this->mediaLinks->removeElement($mediaLink)) {
            // set the owning side to null (unless already changed)
            if ($mediaLink->getMedia() === $this) {
                $mediaLink->setMedia(null);
            }
        }

        return $this;
    }
    public function getName(): string
    {
        return $this->getNameOf($this->getMedia());
    }

    public static function getNameOf($filename): string
    {
        $filename_explode = explode('_', pathinfo($filename)['filename']);
        $last_explode_part    = $filename_explode[count($filename_explode) - 1];
        return str_replace('_'.$last_explode_part, '', $filename);
    }

    /**
     * @return string|null
     */
    public function getMedia(): ?string
    {
        return $this->media;
    }

    /**
     * @param string|null $media
     */
    public function setMedia(?string $media): void
    {
        $this->media = $media;
    }
}
