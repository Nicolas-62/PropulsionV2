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


    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->children = new ArrayCollection();
    }


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

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy:'children')]
    #[ORM\JoinColumn(name:"category_id", referencedColumnName:"id")]
    protected ?Category $parent;

    #[ORM\OneToMany(targetEntity: self::class, mappedBy:"parent")]
    #[ORM\JoinColumn(name:"category_id", referencedColumnName:"id")]
    protected Collection $children;

    /**
     * @var int|null
     *
     * Identifiant du parent [Optional]
     */
    #[ORM\Column(nullable: true)]
    private ?int $category_id = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Article::class, fetch:"EXTRA_LAZY")]
    private Collection $articles;

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

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setCategory($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getCategory() === $this) {
                $article->setCategory(null);
            }
        }

        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    public function setCategoryId(?int $category_id): self
    {
        $this->category_id = $category_id;

        return $this;
    }
}
