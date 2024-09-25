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
    private ?bool $hasFiles = false;
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
    private ?bool $hasSoundcloudLink = false;
    private ?bool $hasSoundcloudSecondLink = false;
    private ?bool $hasSoundcloudThirdLink = false;
    private ?bool $hasStar = false;
    private ?bool $hasDatetimeEvent = false;
    private ?bool $hasDatetimeEndEvent = false;
    private ?bool $hasOrdre = false;
    private ?bool $hasPublicType = false;


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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hash = null;

    #[ORM\Column]
    private ?bool $internal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

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

    public function isInternal(): ?bool
    {
        return $this->internal;
    }

    public function setInternal(bool $internal): self
    {
        $this->internal = $internal;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasDescription(): ?bool
    {
        return $this->hasDescription;
    }

    /**
     * @param bool|null $hasDescription
     */
    public function setHasDescription(?bool $hasDescription): void
    {
        $this->hasDescription = $hasDescription;
    }

    /**
     * @return bool|null
     */
    public function getHasSubArticle(): ?bool
    {
        return $this->hasSubArticle;
    }

    /**
     * @param bool|null $hasSubArticle
     */
    public function setHasSubArticle(?bool $hasSubArticle): void
    {
        $this->hasSubArticle = $hasSubArticle;
    }

    /**
     * @return bool|null
     */
    public function getHasContent(): ?bool
    {
        return $this->hasContent;
    }

    /**
     * @param bool|null $hasContent
     */
    public function setHasContent(?bool $hasContent): void
    {
        $this->hasContent = $hasContent;
    }

    /**
     * @return bool|null
     */
    public function getHasCreate(): ?bool
    {
        return $this->hasCreate;
    }

    /**
     * @param bool|null $hasCreate
     */
    public function setHasCreate(?bool $hasCreate): void
    {
        $this->hasCreate = $hasCreate;
    }

    /**
     * @return bool|null
     */
    public function getHasSubtitle(): ?bool
    {
        return $this->hasSubtitle;
    }

    /**
     * @param bool|null $hasSubtitle
     */
    public function setHasSubtitle(?bool $hasSubtitle): void
    {
        $this->hasSubtitle = $hasSubtitle;
    }

    /**
     * @return bool|null
     */
    public function getHasDateEvent(): ?bool
    {
        return $this->hasDateEvent;
    }

    /**
     * @param bool|null $hasDateEvent
     */
    public function setHasDateEvent(?bool $hasDateEvent): void
    {
        $this->hasDateEvent = $hasDateEvent;
    }

    /**
     * @return bool|null
     */
    public function getHasHeureEvent(): ?bool
    {
        return $this->hasHeureEvent;
    }

    /**
     * @param bool|null $hasHeureEvent
     */
    public function setHasHeureEvent(?bool $hasHeureEvent): void
    {
        $this->hasHeureEvent = $hasHeureEvent;
    }

    /**
     * @return bool|null
     */
    public function getHasYoutubeLink(): ?bool
    {
        return $this->hasYoutubeLink;
    }

    /**
     * @param bool|null $hasYoutubeLink
     */
    public function setHasYoutubeLink(?bool $hasYoutubeLink): void
    {
        $this->hasYoutubeLink = $hasYoutubeLink;
    }

    /**
     * @return bool|null
     */
    public function getHasYoutubeSecondLink(): ?bool
    {
        return $this->hasYoutubeSecondLink;
    }

    /**
     * @param bool|null $hasYoutubeSecondLink
     */
    public function setHasYoutubeSecondLink(?bool $hasYoutubeSecondLink): void
    {
        $this->hasYoutubeSecondLink = $hasYoutubeSecondLink;
    }

    /**
     * @return bool|null
     */
    public function getHasFacebookLink(): ?bool
    {
        return $this->hasFacebookLink;
    }

    /**
     * @param bool|null $hasFacebookLink
     */
    public function setHasFacebookLink(?bool $hasFacebookLink): void
    {
        $this->hasFacebookLink = $hasFacebookLink;
    }

    /**
     * @return bool|null
     */
    public function getHasInstagramLink(): ?bool
    {
        return $this->hasInstagramLink;
    }

    /**
     * @param bool|null $hasInstagramLink
     */
    public function setHasInstagramLink(?bool $hasInstagramLink): void
    {
        $this->hasInstagramLink = $hasInstagramLink;
    }

    /**
     * @return bool|null
     */
    public function getHasFiles(): ?bool
    {
        return $this->hasFiles;
    }

    /**
     * @param bool|null $hasFiles
     */
    public function setHasFiles(?bool $hasFiles): void
    {
        $this->hasFiles = $hasFiles;
    }

    /**
     * @return bool|null
     */
    public function getHasSiteInternet(): ?bool
    {
        return $this->hasSiteInternet;
    }

    /**
     * @param bool|null $hasSiteInternet
     */
    public function setHasSiteInternet(?bool $hasSiteInternet): void
    {
        $this->hasSiteInternet = $hasSiteInternet;
    }

    /**
     * @return bool|null
     */
    public function getHasTwitterLink(): ?bool
    {
        return $this->hasTwitterLink;
    }

    /**
     * @param bool|null $hasTwitterLink
     */
    public function setHasTwitterLink(?bool $hasTwitterLink): void
    {
        $this->hasTwitterLink = $hasTwitterLink;
    }

    /**
     * @return bool|null
     */
    public function getHasCancelled(): ?bool
    {
        return $this->hasCancelled;
    }

    /**
     * @param bool|null $hasCancelled
     */
    public function setHasCancelled(?bool $hasCancelled): void
    {
        $this->hasCancelled = $hasCancelled;
    }

    /**
     * @return bool|null
     */
    public function getHasReported(): ?bool
    {
        return $this->hasReported;
    }

    /**
     * @param bool|null $hasReported
     */
    public function setHasReported(?bool $hasReported): void
    {
        $this->hasReported = $hasReported;
    }

    /**
     * @return bool|null
     */
    public function getHasFull(): ?bool
    {
        return $this->hasFull;
    }

    /**
     * @param bool|null $hasFull
     */
    public function setHasFull(?bool $hasFull): void
    {
        $this->hasFull = $hasFull;
    }

    /**
     * @return bool|null
     */
    public function getHasTicketingLink(): ?bool
    {
        return $this->hasTicketingLink;
    }

    /**
     * @param bool|null $hasTicketingLink
     */
    public function setHasTicketingLink(?bool $hasTicketingLink): void
    {
        $this->hasTicketingLink = $hasTicketingLink;
    }

    /**
     * @return bool|null
     */
    public function getHasTypeMusic(): ?bool
    {
        return $this->hasTypeMusic;
    }

    /**
     * @param bool|null $hasTypeMusic
     */
    public function setHasTypeMusic(?bool $hasTypeMusic): void
    {
        $this->hasTypeMusic = $hasTypeMusic;
    }

    /**
     * @return bool|null
     */
    public function getHasOrigin(): ?bool
    {
        return $this->hasOrigin;
    }

    /**
     * @param bool|null $hasOrigin
     */
    public function setHasOrigin(?bool $hasOrigin): void
    {
        $this->hasOrigin = $hasOrigin;
    }

    /**
     * @return bool|null
     */
    public function getHasStyle(): ?bool
    {
        return $this->hasStyle;
    }

    /**
     * @param bool|null $hasStyle
     */
    public function setHasStyle(?bool $hasStyle): void
    {
        $this->hasStyle = $hasStyle;
    }

    /**
     * @return bool|null
     */
    public function getHasThemeBackColor(): ?bool
    {
        return $this->hasThemeBackColor;
    }

    /**
     * @param bool|null $hasThemeBackColor
     */
    public function setHasThemeBackColor(?bool $hasThemeBackColor): void
    {
        $this->hasThemeBackColor = $hasThemeBackColor;
    }

    /**
     * @return bool|null
     */
    public function getHasThemeTextColor(): ?bool
    {
        return $this->hasThemeTextColor;
    }

    /**
     * @param bool|null $hasThemeTextColor
     */
    public function setHasThemeTextColor(?bool $hasThemeTextColor): void
    {
        $this->hasThemeTextColor = $hasThemeTextColor;
    }

    /**
     * @return bool|null
     */
    public function getHasStyleBackColor(): ?bool
    {
        return $this->hasStyleBackColor;
    }

    /**
     * @param bool|null $hasStyleBackColor
     */
    public function setHasStyleBackColor(?bool $hasStyleBackColor): void
    {
        $this->hasStyleBackColor = $hasStyleBackColor;
    }

    /**
     * @return bool|null
     */
    public function getHasStyleTextColor(): ?bool
    {
        return $this->hasStyleTextColor;
    }

    /**
     * @param bool|null $hasStyleTextColor
     */
    public function setHasStyleTextColor(?bool $hasStyleTextColor): void
    {
        $this->hasStyleTextColor = $hasStyleTextColor;
    }

    /**
     * @return bool|null
     */
    public function getHasSoundcloudLink(): ?bool
    {
        return $this->hasSoundcloudLink;
    }

    /**
     * @param bool|null $hasSoundcloudLink
     */
    public function setHasSoundcloudLink(?bool $hasSoundcloudLink): void
    {
        $this->hasSoundcloudLink = $hasSoundcloudLink;
    }

    /**
     * @return bool|null
     */
    public function getHasSoundcloudSecondLink(): ?bool
    {
        return $this->hasSoundcloudSecondLink;
    }

    /**
     * @param bool|null $hasSoundcloudSecondLink
     */
    public function setHasSoundcloudSecondLink(?bool $hasSoundcloudSecondLink): void
    {
        $this->hasSoundcloudSecondLink = $hasSoundcloudSecondLink;
    }

    /**
     * @return bool|null
     */
    public function getHasSoundcloudThirdLink(): ?bool
    {
        return $this->hasSoundcloudThirdLink;
    }

    /**
     * @param bool|null $hasSoundcloudThirdLink
     */
    public function setHasSoundcloudThirdLink(?bool $hasSoundcloudThirdLink): void
    {
        $this->hasSoundcloudThirdLink = $hasSoundcloudThirdLink;
    }

    /**
     * @return bool|null
     */
    public function getHasStar(): ?bool
    {
        return $this->hasStar;
    }

    /**
     * @param bool|null $hasStar
     */
    public function setHasStar(?bool $hasStar): void
    {
        $this->hasStar = $hasStar;
    }

    /**
     * @return bool|null
     */
    public function getHasDatetimeEvent(): ?bool
    {
        return $this->hasDatetimeEvent;
    }

    /**
     * @param bool|null $hasDatetimeEvent
     */
    public function setHasDatetimeEvent(?bool $hasDatetimeEvent): void
    {
        $this->hasDatetimeEvent = $hasDatetimeEvent;
    }

    /**
     * @return bool|null
     */
    public function getHasDatetimeEndEvent(): ?bool
    {
        return $this->hasDatetimeEndEvent;
    }

    /**
     * @param bool|null $hasDatetimeEndEvent
     */
    public function setHasDatetimeEndEvent(?bool $hasDatetimeEndEvent): void
    {
        $this->hasDatetimeEndEvent = $hasDatetimeEndEvent;
    }

    /**
     * @return bool|null
     */
    public function getHasOrdre(): ?bool
    {
        return $this->hasOrdre;
    }

    /**
     * @param bool|null $hasOrdre
     */
    public function setHasOrdre(?bool $hasOrdre): void
    {
        $this->hasOrdre = $hasOrdre;
    }

}
