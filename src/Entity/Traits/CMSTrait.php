<?php

namespace App\Entity\Traits;

use App\Entity\Category;
use App\Entity\Langues;
use App\Entity\Online;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

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

    public function getOnlineByLangue($langue = null): false|Online
    {

        $onlines = $this->getOnlines()->filter(function(Online $online, $langue) {
            if($langue == null){
                $code = 'fr';
            }else{
                if(  is_int($langue) )
                {
                    $langue = $this->entityManager->getRepository(Langues::class)->find($langue);
                }
                $code = $langue->getCode();
            }
            //dump($code);

            return $online->getLangue()->getCode() == $code;
        })->first();

        return $onlines;
    }

    public function isOnline($langue = null): bool
    {
        $online = $this->getOnlineByLangue($langue);
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

}