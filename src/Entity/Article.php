<?php

namespace App\Entity;

use App\Entity\Traits\CMSTrait;
use App\Entity\Traits\TimesTampableTrait;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityBuiltEvent;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    use CMSTrait;
    use TimestampableTrait;


    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->online = new ArrayCollection();
    }


    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: self::class, cascade: ['remove'], inversedBy: 'children')]
    #[ORM\JoinColumn(name:"article_id", referencedColumnName:"id")]
    protected ?Article $parent;

    #[ORM\OneToMany(mappedBy:"parent", targetEntity: self::class, cascade: ['remove'])]
    #[ORM\JoinColumn(name:"article_id", referencedColumnName:"id")]
    protected Collection $children;

    #[ORM\Column(nullable: true)]
    private ?int $article_id = null;

    #[ORM\ManyToOne(cascade: ['persist'],inversedBy: 'articles')]
    private ?Category $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $illustration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $illustration2 = null;


    #[ORM\OneToMany(mappedBy: "article", targetEntity: 'App\Entity\Media',cascade: ['persist','remove'] )]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $media;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Online::class,cascade: ['remove'],)]
    private Collection $online;


    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getArticleId(): ?int
    {
        return $this->article_id;
    }

    public function setArticleId(?int $article_id): self
    {
        $this->article_id = $article_id;

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

    public function getIllustration(): ?string
    {
        return $this->illustration;
    }

    public function setIllustration(?string $illustration): self
    {
        $this->illustration = $illustration;

        return $this;
    }

    public function getIllustration2(): ?string
    {
        return $this->illustration2;
    }

    public function setIllustration2(?string $illustration2): self
    {
        $this->illustration2 = $illustration2;

        return $this;
    }


    public function __toString(): string
    {
        if($this->title) {
            return $this->title;
        }else{
            return '';
        }
    }

    /**
     * @return Collection
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    /**
     * @param Collection $media
     */
    public function setMedia(Collection $media): void
    {
        $this->media = $media;
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
            $online->setArticle($this);
        }

        return $this;
    }

    public function removeOnline(Online $online): self
    {
        if ($this->online->removeElement($online)) {
            // set the owning side to null (unless already changed)
            if ($online->getArticle() === $this) {
                $online->setArticle(null);
            }
        }

        return $this;
    }
}


