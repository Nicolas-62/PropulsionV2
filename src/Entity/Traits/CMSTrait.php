<?php

namespace App\Entity\Traits;

use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Language;
use App\Entity\Media;
use App\Entity\Mediaspec;
use App\Entity\Online;
use App\Entity\Seo;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;
use phpDocumentor\Reflection\Types\Boolean;
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

    /**
     * @param $code_langue
     * @return mixed
     */
    public function getSeo($code_langue = null): mixed
    {
        // Todo : modifier la récup de la langue
        if($code_langue == null){
            // On récupère la langue du site par défaut.
            $code_langue = $_ENV['LOCALE'];
        }
        $seo = $this->seo->filter(function(Seo $seo) use ($code_langue) {
            return $seo->getLanguage()->getCode() === $code_langue;
        })->first();

        return ($seo) ? $seo : null;
    }

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

    /**
     * @return Array
     */
    public function getChildrenIds(): Array
    {
        $childrenIds = [];
        foreach ($this->getChildren() as $child){
            $childrenIds[] = $child->getId();
        }
        return $childrenIds;
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
        // On filtre les articles en ligne par leur code langue.
        $online = $this->getOnlines()->filter(function(Online $online) use ($code_langue) {
            return $online->getLanguage()->getCode() === $code_langue;
        })->first();

        return $online;
    }

    public function isOnline($code_langue = null): bool
    {
        // Todo : modifier la récup de la langue
        if($code_langue == null){
            // On récupère la langue du site par défaut.
            $code_langue = $_ENV['LOCALE'];
        }
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



    public function getMedia(int $mediaspec_id): ?Media
    {
        $media = null;
        foreach ($this->getMediaLinks() as $mediaLink){
            if($mediaLink->getMediaspec()->getId() == $mediaspec_id){
                $media = $mediaLink->getMedia();
            }
        }
        return $media;
    }


    /**
     * Récupère les ancetres de l'objet dans un array
     *
     * @param $ancestors
     * @return ArrayCollection
     */
    public function getAncestors($ancestors = null): ArrayCollection
    {
        if($ancestors == null) {
            $ancestors = new ArrayCollection();
        }
        // Si l'entite a un parent de la même famille..
        if($this->getParent() != null) {
            $ancestors->add($this->getParent());
            $this->getParent()->getAncestors($ancestors);
        }
        // Si l'entité est un article est posède une catégorie parent.
        else if($this instanceof Article && $this->getCategory() != null){
            $ancestors->add($this->getCategory());
            $this->getCategory()->getAncestors($ancestors);
        }

        return $ancestors;
    }


    /**
     * Retourne la première catégorie parent trouvée.
     *
     * @return Category|null
     */
    public function getCategoryParent(): ?Category
    {
        if($this->getCategory() != null){
            return $this->getCategory();
        }
        foreach($this->getAncestors() as $ancestor){
            if($ancestor instanceof Category){
                return $ancestor;
            }
        }
        return null;
    }

}