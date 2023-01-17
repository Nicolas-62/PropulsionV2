<?php

namespace App\Entity;

use App\Entity\Traits\CMSTrait;
use App\Entity\Traits\TimesTampableTrait;
use App\Repository\SectionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SectionRepository::class)]
class Section
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

    #[ORM\ManyToOne(inversedBy: 'sections')]
    private ?Category $category = null;



    public function canCreate(): ?bool
    {
        return $this->canCreate;
    }

    public function setCanCreate(bool $canCreate): self
    {
        $this->canCreate = $canCreate;

        return $this;
    }

    public function hasMulti(): ?bool
    {
        return $this->hasMulti;
    }

    public function setHasMulti(bool $hasMulti): self
    {
        $this->hasMulti = $hasMulti;

        return $this;
    }

    public function hasTheme(): ?bool
    {
        return $this->hasTheme;
    }

    public function setHasTheme(bool $hasTheme): self
    {
        $this->hasTheme = $hasTheme;

        return $this;
    }

    public function hasTitle(): ?bool
    {
        return $this->hasTitle;
    }

    public function setHasTitle(bool $hasTitle): self
    {
        $this->hasTitle = $hasTitle;

        return $this;
    }

    public function hasSubTitle(): ?bool
    {
        return $this->hasSubTitle;
    }

    public function setHasSubTitle(bool $hasSubTitle): self
    {
        $this->hasSubTitle = $hasSubTitle;

        return $this;
    }

    public function hasContent(): ?bool
    {
        return $this->hasContent;
    }

    public function setHasContent(bool $hasContent): self
    {
        $this->hasContent = $hasContent;

        return $this;
    }

    public function hasSeo(): ?bool
    {
        return $this->hasSeo;
    }

    public function setHasSeo(bool $hasSeo): self
    {
        $this->hasSeo = $hasSeo;

        return $this;
    }

    public function hasLink(): ?bool
    {
        return $this->hasLink;
    }

    public function setHasLink(bool $hasLink): self
    {
        $this->hasLink = $hasLink;

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

