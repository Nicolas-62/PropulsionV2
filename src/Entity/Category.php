<?php

namespace App\Entity;

use App\Entity\ExtraDataTrait\CategoryDataTrait;
use App\Entity\Traits\CMSTrait;
use App\Entity\Traits\ErrorTrait;
use App\Entity\Traits\ExtraDataTrait;
use App\Entity\Traits\ExtraFieldtrait;
use App\Entity\Traits\LanguageTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\TimesTampableTrait;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

// Récupération des champs spécifiques à l'instance dans le csv associé.
$fields = array();
$filesystem = new Filesystem();
if($filesystem->exists(__DIR__.'/ExtraFields/CategoryData.csv')){
    $file = new File(__DIR__.'/ExtraFields/CategoryData.csv');
    $csvEncoder = new CsvEncoder();
    $fields = $csvEncoder->decode($file->getContent(), 'array');
}
define('CATEGORY_DATA_FIELDS', $fields);
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    use CMSTrait;
    // Champs date.
    use TimestampableTrait;
    use ExtraFieldTrait;
    use LanguageTrait;
    use MediaTrait;
    use ErrorTrait;

    // Champs spécifiques !! à synchroniser manuellement avec les champs définis dans le csv
    // !! Ajouter les getters et les setters également, définir une valeur par défaut.
    private ?string $titleByLanguage = '';
    private ?bool $hasCreate = true;
    private ?bool $hasSubArticle = false;
    private ?bool $hasDescription = false;
    private ?bool $hasContent = false;
    private ?bool $hasSubtitle = false;
    private ?bool $hasDateEvent = false;
    private ?bool $hasHeureEvent = false;
    private ?bool $hasYoutubeLink = false;
    private ?bool $hasYoutubeSecondLink = false;
    private ?bool $hasFacebookLink = false;
    private ?bool $hasInstagramLink = false;
    private ?bool $hasSiteInternet = false;
    private ?bool $hasTwitterLink = false;
    private ?bool $hasCancelled = false;
    private ?bool $hasReported = false;
    private ?bool $hasFull = false;
    private ?bool $hasTicketingLink = false;
    private ?bool $hasTypeMusic = false;
    private ?bool $hasOrigin = false;
    private ?bool $hasStyle = false;
    private ?bool $hasThemeBackColor = false;
    private ?bool $hasThemeTextColor = false;
    private ?bool $hasStyleBackColor = false;
    private ?bool $hasStyleTextColor = false;

    // Liste des champs supplémentaires spécifiques.
    private array $extraFields = CATEGORY_DATA_FIELDS;

    public function __construct()
    {
        $this->articles         = new ArrayCollection();
        $this->children         = new ArrayCollection();
        $this->mediaspecs       = new ArrayCollection();
        $this->onlines          = new ArrayCollection();
        $this->mediaLinks       = new ArrayCollection();
        $this->created_at       = new \DateTimeImmutable();
        $this->updated_at       = new \DateTimeImmutable();
        $this->seo              = new ArrayCollection();
    }


    #[ORM\Column]
    private ?bool $hasTheme = null;

    #[ORM\Column]
    private ?bool $hasSeo = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name:"category_id", referencedColumnName:"id")]
    protected ?Category $parent;


    #[ORM\OneToMany( mappedBy: "parent", targetEntity: self::class, cascade: ['remove'])]
    #[ORM\JoinColumn(name:"category_id", referencedColumnName:"id")]
    protected Collection $children;


    /**
     * @var int|null
     * Identifiant du parent [Optional]
     */
    #[ORM\Column(nullable: true)]
    private ?int $category_id = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Article::class, cascade: ['remove'], fetch: "EXTRA_LAZY")]
    private ?Collection $articles;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Online::class, cascade: ['remove'])]
    private Collection $onlines;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: MediaLink::class, cascade: ['remove'])]
    private Collection $mediaLinks;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Mediaspec::class)]
    private Collection $mediaspecs;

    #[ORM\OneToMany(mappedBy: 'object', targetEntity: CategoryData::class, orphanRemoval: true)]
    private Collection $data;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Seo::class, cascade: ['persist','remove'])]
    private Collection $seo;

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

    /**
     * Methode nessecaire pour appel des elements dans un sélecteur de formulaire.
     *
     * @return string
     */
    public function __toString(): string
    {
        if($this->title) {
            return $this->title;
        }else{
            return '';
        }
    }


    public function setCategoryId(?int $category_id): self
    {
        $this->category_id = $category_id;

        return $this;
    }

    /**
     * @return Collection<int, Online>
     */
    public function getOnlines(): Collection
    {
        return $this->onlines;
    }

    public function addOnline(Online $online): self
    {
        if (!$this->onlines->contains($online)) {
            $this->onlines->add($online);
            $online->setCategory($this);
        }

        return $this;
    }

    public function removeOnline(Online $online): self
    {
        if ($this->onlines->removeElement($online)) {
            // set the owning side to null (unless already changed)
            if ($online->getCategory() === $this) {
                $online->setCategory(null);
            }
        }

        return $this;
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
            $mediaLink->setCategory($this);
        }

        return $this;
    }

    public function removeMediaLink(MediaLink $mediaLink): self
    {
        if ($this->mediaLinks->removeElement($mediaLink)) {
            // set the owning side to null (unless already changed)
            if ($mediaLink->getCategory() === $this) {
                $mediaLink->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mediaspec>
     */
    public function getMediaspecs(): Collection
    {
        return $this->mediaspecs;
    }

    public function addMediaspec(Mediaspec $mediaspec): self
    {
        if (!$this->mediaspecs->contains($mediaspec)) {
            $this->mediaspecs->add($mediaspec);
            $mediaspec->setCategory($this);
        }

        return $this;
    }

    public function removeMediaspec(Mediaspec $mediaspec): self
    {
        if ($this->mediaspecs->removeElement($mediaspec)) {
            // set the owning side to null (unless already changed)
            if ($mediaspec->getCategory() === $this) {
                $mediaspec->setCategory(null);
            }
        }

        return $this;
    }

    public function addSeo(Seo $seo): self
    {
        if (!$this->seo->contains($seo)) {
            $this->seo->add($seo);
            $seo->setCategory($this);
        }

        return $this;
    }

    public function removeSeo(Seo $seo): self
    {
        if ($this->seo->removeElement($seo)) {
            // set the owning side to null (unless already changed)
            if ($seo->getCategory() === $this) {
                $seo->setCategory(null);
            }
        }

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

    public function hasSeo(): ?bool
    {
        return $this->hasSeo;
    }

    public function setHasSeo(bool $hasSeo): self
    {
        $this->hasSeo = $hasSeo;

        return $this;
    }
}
