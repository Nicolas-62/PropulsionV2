<?php

namespace App\Entity;

use App\Entity\Traits\CMSTrait;
use App\Entity\Traits\TimesTampableTrait;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
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
        $this->mediaspecs = new ArrayCollection();
        $this->online = new ArrayCollection();
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

    #[ORM\ManyToOne(targetEntity: self::class, cascade: ['remove'], inversedBy: 'children',)]
    #[ORM\JoinColumn(name:"category_id", referencedColumnName:"id")]
    protected ?Category $parent;

    #[ORM\OneToMany( mappedBy:"parent", targetEntity: self::class,  cascade: ['remove'])]
    #[ORM\JoinColumn(name:"category_id", referencedColumnName:"id")]
    protected Collection $children;


    #[ORM\OneToMany(mappedBy: "category",targetEntity: 'App\Entity\Media', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $media;

    /**
     * @var int|null
     *
     * Identifiant du parent [Optional]
     */
    #[ORM\Column(nullable: true)]
    private ?int $category_id = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Article::class, cascade: ['remove'], fetch: "EXTRA_LAZY")]
    private Collection $articles;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Mediaspecs::class)]
    private Collection $mediaspecs;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Online::class,cascade: ['remove'],)]
    private Collection $online;




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

    public function __toString(): string
    {
        if($this->title) {
            return $this->title;
        }else{
            return 'Cat';
        }
    }


    public function setCategoryId(?int $category_id): self
    {
        $this->category_id = $category_id;

        return $this;
    }

    /**
     * @return Collection<int, Mediaspecs>
     */
    public function getMediaspecs(): Collection
    {
        return $this->mediaspecs;
    }

    public function addMediaspec(Mediaspecs $mediaspec): self
    {
        if (!$this->mediaspecs->contains($mediaspec)) {
            $this->mediaspecs->add($mediaspec);
            $mediaspec->setCategory($this);
        }

        return $this;
    }

    public function removeMediaspec(Mediaspecs $mediaspec): self
    {
        if ($this->mediaspecs->removeElement($mediaspec)) {
            // set the owning side to null (unless already changed)
            if ($mediaspec->getCategory() === $this) {
                $mediaspec->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Online>
     */
    public function getOnline(): Collection
    {
        return $this->online;
    }

    public function addOnline(Online $online): self
    {
        if (!$this->online->contains($online)) {
            $this->online->add($online);
            $online->setCategory($this);
        }

        return $this;
    }

    public function removeOnline(Online $online): self
    {
        if ($this->online->removeElement($online)) {
            // set the owning side to null (unless already changed)
            if ($online->getCategory() === $this) {
                $online->setCategory(null);
            }
        }

        return $this;
    }


    public function getOnlineByLangue($langue = null): false|Online
    {

        $online = $this->getOnline()->filter(function(Online $online, $langue) {
            if($langue == null){
                $code = 'fr';
            }else{
                $code = $langue->getCode();
            }
            //dump($code);

            return $online->getLangue()->getCode() == $code;
        })->first();

        return $online;
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


}
