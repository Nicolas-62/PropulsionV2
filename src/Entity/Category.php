<?php

namespace App\Entity;

use App\Entity\Traits\CMSTrait;
use App\Entity\Traits\TimesTampableTrait;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    use CMSTrait;
    use TimestampableTrait;

    #[ORM\Column]
    private ?bool $canCreate = null;

    #[ORM\Column]
    private ?bool $hasMulti = null;

    #[ORM\Column]
    private ?bool $hasTheme = null;

    #[ORM\Column]
    private ?bool $hasTitle = null;

    #[ORM\Column]
    private ?bool $hasSubTitle = null;

    #[ORM\Column]
    private ?bool $hasContent = null;

    #[ORM\Column]
    private ?bool $hasSeo = null;

    #[ORM\Column]
    private ?bool $hasLink = null;


    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Section::class, fetch:"EXTRA_LAZY")]
    private Collection $sections;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy:'children')]
    #[ORM\JoinColumn(name:"category_id", referencedColumnName:"id")]
    protected ?Category $parent;

    #[ORM\OneToMany(targetEntity: self::class, mappedBy:"parent")]
    #[ORM\JoinColumn(name:"category_id", referencedColumnName:"id")]
    protected ArrayCollection $children;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->children = new ArrayCollection();
    }


    public function isCanCreate(): ?bool
    {
        return $this->canCreate;
    }

    public function setCanCreate(bool $canCreate): self
    {
        $this->canCreate = $canCreate;

        return $this;
    }

    public function isHasMulti(): ?bool
    {
        return $this->hasMulti;
    }

    public function setHasMulti(bool $hasMulti): self
    {
        $this->hasMulti = $hasMulti;

        return $this;
    }

    public function isHasTheme(): ?bool
    {
        return $this->hasTheme;
    }

    public function setHasTheme(bool $hasTheme): self
    {
        $this->hasTheme = $hasTheme;

        return $this;
    }

    public function isHasTitle(): ?bool
    {
        return $this->hasTitle;
    }

    public function setHasTitle(bool $hasTitle): self
    {
        $this->hasTitle = $hasTitle;

        return $this;
    }

    public function isHasSubTitle(): ?bool
    {
        return $this->hasSubTitle;
    }

    public function setHasSubTitle(bool $hasSubTitle): self
    {
        $this->hasSubTitle = $hasSubTitle;

        return $this;
    }

    public function isHasContent(): ?bool
    {
        return $this->hasContent;
    }

    public function setHasContent(bool $hasContent): self
    {
        $this->hasContent = $hasContent;

        return $this;
    }

    public function isHasSeo(): ?bool
    {
        return $this->hasSeo;
    }

    public function setHasSeo(bool $hasSeo): self
    {
        $this->hasSeo = $hasSeo;

        return $this;
    }

    public function isHasLink(): ?bool
    {
        return $this->hasLink;
    }

    public function setHasLink(bool $hasLink): self
    {
        $this->hasLink = $hasLink;

        return $this;
    }

    /**
     * @return Collection<int, Section>
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): self
    {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
            $section->setCategory($this);
        }

        return $this;
    }

    public function removeSection(Section $section): self
    {
        if ($this->sections->removeElement($section)) {
            // set the owning side to null (unless already changed)
            if ($section->getCategory() === $this) {
                $section->setCategory(null);
            }
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
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
    public function setChildren(ArrayCollection $children): Category
    {
        $this->children = $children;
        return $this;
    }
}
