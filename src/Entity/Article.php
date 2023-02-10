<?php

namespace App\Entity;

use App\Entity\Traits\CMSTrait;
use App\Entity\Traits\TimesTampableTrait;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    use CMSTrait;
    use TimestampableTrait;


    public function __construct()
    {
        $this->children = new ArrayCollection();
    }


    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy:'children')]
    #[ORM\JoinColumn(name:"article_id", referencedColumnName:"id")]
    protected ?Article $parent;

    #[ORM\OneToMany(mappedBy:"parent", targetEntity: self::class)]
    #[ORM\JoinColumn(name:"article_id", referencedColumnName:"id")]
    protected Collection $children;


    private ?Article $grandParent = null;

    public function getGrandParent(): ?Article
    {
        if($this->parent !== null){
            $this->grandParent = $this->parent->getParent();
        }
        return $this->grandParent;
    }

    #[ORM\Column(nullable: true)]
    private ?int $article_id = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?Category $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $illustration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $illustration2 = null;


    #[ORM\OneToMany(mappedBy: "article", targetEntity: 'App\Entity\Media',cascade: ['persist'] )]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $media;


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
        return $this->title;
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

}


