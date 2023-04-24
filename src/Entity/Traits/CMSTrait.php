<?php

namespace App\Entity\Traits;

use App\Constants\Constants;
use App\Entity\Category;
use App\Entity\Language;
use App\Entity\Media;
use App\Entity\Mediaspec;
use App\Entity\Online;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Locale;

#[UniqueEntity("title")]
trait CMSTrait
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    protected ?string $title = null;

    #[ORM\Column]
    private ?int $ordre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        $slugify = new Slugify();
        return $slugify->slugify($this->title);
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

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }


    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function getParentId(): ?int
    {
        if($this->getParent() != null){
            return $this->getParent()->getId();
        }else{
            return null;
        }
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @param ArrayCollection $children
     * @return Category
     */
    public function setChildren(ArrayCollection $children): self
    {
        $this->children = $children;
        return $this;
    }

    public function getOnlineByCodeLangue($code_langue): false|Online
    {
        // Si pas de langue précisé.
        if($code_langue == null){
            // On récupère la langue du site par défaut.
            $code_langue = $_ENV['LOCALE'];
        }
        // On filtre les articles en ligne par leur code langue.
        $online = $this->getOnlines()->filter(function(Online $online) use ($code_langue) {
            return $online->getLangue()->getCode() === $code_langue;
        })->first();

        return $online;
    }

    public function isOnline($code_langue = null): bool
    {

        $online = $this->getOnlineByCodeLangue($code_langue);
        if($online &&  $online->isOnline()){
            return true;
        }else{
            return false;
        }
    }

    public function getClassName(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }



    public function getMedia(Mediaspec $mediaspec): ?Media
    {
        $media = null;
        foreach ($this->getMediaLinks() as $mediaLink){
            if($mediaLink->getMediaspec() === $mediaspec){
                $media = $mediaLink->getMedia();
            }
        }
        return $media;
    }

}