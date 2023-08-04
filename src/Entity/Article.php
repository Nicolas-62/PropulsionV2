<?php

namespace App\Entity;

use App\Entity\Traits\CMSTrait;
use App\Entity\Traits\ExtraDataTrait;
use App\Entity\Traits\ExtraFieldtrait;
use App\Entity\Traits\LanguageTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\PreviewTrait;
use App\Entity\Traits\TimesTampableTrait;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityBuiltEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    use CMSTrait;
    // Champs date.
    use TimestampableTrait;
    use ExtraFieldtrait;
    use MediaTrait;
    use LanguageTrait;

    // Champs supplémentaires
    private ?string $content          = '';
    private ?string $titleByLanguage  = '';
    private ?string $subtitle  = '';
    private ?string $date  = '';
    private ?string $youtubeLink  = '';
    private ?string $facebookLink  = '';
    private ?string $instagramLink  = '';
    private ?string $dateEvent  = '';
    private ?string $heureEvent  = '';
    private ?bool $cancelled  = false;
    private ?string $ticketingLink  = '';
    private ?string $description  = '';
    private ?string $typeMusic  = '';
    private ?string $youtubeSecondLink  = '';
    private ?string $siteInternet  = '';
    private ?string $twitterLink  = '';
    private ?string $origin  = '';
    private ?string $themeBackColor  = '';
    private ?string $themeTextColor  = '';
    private ?string $styleBackColor  = '';
    private ?string $styleTextColor  = '';
    private ?string $style  = '';


    // Liste des champs supplémentaires spécifiques.
    private array $extraFields = [
        ['name' => 'titleByLanguage',   'label' => "Titre",                         'ea_type' => 'TextField'],
        ['name' => 'description',       'label' => "Description",                   'ea_type' => 'TextEditorField'],
        ['name' => 'content',           'label' => "Contenu",                       'ea_type' => 'TextEditorField'],
        ['name' => 'subtitle',          'label' => "Sous-titre",                    'ea_type' => 'TextField'],
        ['name' => 'dateEvent',         'label' => "Date de l'évènement",           'ea_type' => 'TextField'],
        ['name' => 'heureEvent',        'label' => "Heure de l'évènement",          'ea_type' => 'TextField'],
        ['name' => 'youtubeLink',       'label' => "Lien Youtube",                  'ea_type' => 'TextField'],
        ['name' => 'youtubeSecondLink', 'label' => "2ème Lien Youtube",             'ea_type' => 'TextField'],
        ['name' => 'facebookLink',      'label' => "Lien Facebook",                 'ea_type' => 'TextField'],
        ['name' => 'instagramLink',     'label' => "Lien Instagram",                'ea_type' => 'TextField'],
        ['name' => 'siteInternet',      'label' => "Site internet",                 'ea_type' => 'TextField'],
        ['name' => 'twitterLink',       'label' => "Lien Twitter",                  'ea_type' => 'TextField'],
        ['name' => 'cancelled',         'label' => "Evenement annulé",              'ea_type' => 'BooleanField'],
        ['name' => 'ticketingLink',     'label' => "Lien Billetterie",              'ea_type' => 'TextField'],
        ['name' => 'typeMusic',         'label' => "Type de musique",               'ea_type' => 'TextField'],
        ['name' => 'origin',            'label' => "Origine",                       'ea_type' => 'TextField'],
        ['name' => 'style',             'label' => "Style",                         'ea_type' => 'TextField'],
        ['name' => 'themeBackColor',    'label' => "Couleur de fond du thème",      'ea_type' => 'ColorField'],
        ['name' => 'themeTextColor',    'label' => "Couleur du texte du thème",     'ea_type' => 'ColorField'],
        ['name' => 'styleBackColor',    'label' => "Couleur de fond du style",      'ea_type' => 'ColorField'],
        ['name' => 'styleTextColor',    'label' => "Couleur du texte du style",     'ea_type' => 'ColorField'],
    ];




    public function __construct()
    {
        $this->children     = new ArrayCollection();
        $this->onlines      = new ArrayCollection();
        $this->mediaLinks   = new ArrayCollection();
        $this->mediaspecs   = new ArrayCollection();
        $this->created_at   = new \DateTimeImmutable();
        $this->updated_at   = new \DateTimeImmutable();
        $this->articleData  = new ArrayCollection();
        $this->data         = new ArrayCollection();
        $this->themes       = new ArrayCollection();
        $this->seo          = new ArrayCollection();
    }


    #[ORM\ManyToOne(targetEntity: self::class, cascade: ['remove'], inversedBy: 'children')]
    #[ORM\JoinColumn(name:"article_id", referencedColumnName:"id")]
    protected ?Article $parent;

    #[ORM\OneToMany(mappedBy:"parent", targetEntity: self::class, cascade: ['remove'])]
    #[ORM\JoinColumn(name:"article_id", referencedColumnName:"id")]
    protected Collection $children;

    #[ORM\Column(nullable: true)]
    private ?int $article_id = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'articles')]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Online::class, cascade: ['remove'])]
    private Collection $onlines;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: MediaLink::class, cascade: ['remove'])]
    private Collection $mediaLinks;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Mediaspec::class)]
    private Collection $mediaspecs;

    #[ORM\OneToMany(mappedBy: 'object', targetEntity: ArticleData::class, orphanRemoval: true)]
    private Collection $data;

    #[ORM\ManyToMany(targetEntity: Theme::class, inversedBy: 'articles')]
    private Collection $themes;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Seo::class)]
    private Collection $seo;

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

        return $seo;
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

    public function __toString(): string
    {
        if($this->title) {
            return $this->title;
        }else{
            return '';
        }
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
            $online->setArticle($this);
        }
        return $this;
    }

    public function removeOnline(Online $online): self
    {
        if ($this->onlines->removeElement($online)) {
            // set the owning side to null (unless already changed)
            if ($online->getArticle() === $this) {
                $online->setArticle(null);
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
        foreach($this->getMediaLinks() as $mediaLinked){
            if($mediaLinked->getMediaspec()->getId() == $mediaLink ->getMediaspec()->getId()){
               $this->removeMediaLink($mediaLinked);
            }
        }
        $this->mediaLinks->add($mediaLink);
        $mediaLink->setArticle($this);
        return $this;
    }

    public function removeMediaLink(MediaLink $mediaLink): self
    {
        if ($this->mediaLinks->removeElement($mediaLink)) {
            // set the owning side to null (unless already changed)
            if ($mediaLink->getArticle() === $this) {
                $mediaLink->setArticle(null);
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
            $mediaspec->setArticle($this);
        }

        return $this;
    }

    public function removeMediaspec(Mediaspec $mediaspec): self
    {
        if ($this->mediaspecs->removeElement($mediaspec)) {
            // set the owning side to null (unless already changed)
            if ($mediaspec->getArticle() === $this) {
                $mediaspec->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Theme>
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addTheme(Theme $theme): self
    {
        if (!$this->themes->contains($theme)) {
            $this->themes->add($theme);
        }

        return $this;
    }

    public function removeTheme(Theme $theme): self
    {
        $this->themes->removeElement($theme);

        return $this;
    }


    public function addSeo(Seo $seo): self
    {
        if (!$this->seo->contains($seo)) {
            $this->seo->add($seo);
            $seo->setArticle($this);
        }

        return $this;
    }

    public function removeSeo(Seo $seo): self
    {
        if ($this->seo->removeElement($seo)) {
            // set the owning side to null (unless already changed)
            if ($seo->getArticle() === $this) {
                $seo->setArticle(null);
            }
        }

        return $this;
    }

    // ! EXTRA GETTERS & SETTERS

    public function getContent(): ?string
    {
        return $this->content;

    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitleByLanguage(): ?string
    {
        return $this->titleByLanguage;
    }

    /**
     * @param string|null $titleByLanguage
     */
    public function setTitleByLanguage(?string $titleByLanguage): void
    {
        $this->titleByLanguage = $titleByLanguage;
    }

    /**
     * @return string|null
     */
    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    /**
     * @param string|null $subtitle
     */
    public function setSubtitle(?string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    /**
     * @return string|null
     */
    public function getDate(): ?string
    {
        return $this->date;
    }

    /**
     * @param string|null $date
     */
    public function setDate(?string $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string|null
     */
    public function getYoutubeLink(): ?string
    {
        return $this->youtubeLink;
    }

    /**
     * @param string|null $youtubeLink
     */
    public function setYoutubeLink(?string $youtubeLink): void
    {
        $this->youtubeLink = $youtubeLink;
    }

    /**
     * @return string|null
     */
    public function getFacebookLink(): ?string
    {
        return $this->facebookLink;
    }

    /**
     * @param string|null $facebookLink
     */
    public function setFacebookLink(?string $facebookLink): void
    {
        $this->facebookLink = $facebookLink;
    }

    /**
     * @return string|null
     */
    public function getInstagramLink(): ?string
    {
        return $this->instagramLink;
    }

    /**
     * @param string|null $instagramLink
     */
    public function setInstagramLink(?string $instagramLink): void
    {
        $this->instagramLink = $instagramLink;
    }


    /**
     * @return string|null
     */
    public function getDateEvent(): ?string
    {
        return $this->dateEvent;
    }

    /**
     * @param string|null $dateEvent
     */
    public function setDateEvent(?string $dateEvent): void
    {
        $this->dateEvent = $dateEvent;
    }

    /**
     * @return string|null
     */
    public function getHeureEvent(): ?string
    {
        return $this->heureEvent;
    }

    /**
     * @param string|null $heureEvent
     */
    public function setHeureEvent(?string $heureEvent): void
    {
        $this->heureEvent = $heureEvent;
    }

    /**
     * @return bool|null
     */
    public function getCancelled(): ?bool
    {
        return $this->cancelled;
    }

    /**
     * @param bool|null $cancelled
     */
    public function setCancelled(?bool $cancelled): void
    {
        $this->cancelled = $cancelled;
    }

    /**
     * @return string|null
     */
    public function getTicketingLink(): ?string
    {
        return $this->ticketingLink;
    }

    /**
     * @param string|null $ticketingLink
     */
    public function setTicketingLink(?string $ticketingLink): void
    {
        $this->ticketingLink = $ticketingLink;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getTypeMusic(): ?string
    {
        return $this->typeMusic;
    }

    /**
     * @param string|null $typeMusic
     */
    public function setTypeMusic(?string $typeMusic): void
    {
        $this->typeMusic = $typeMusic;
    }

    /**
     * @return string|null
     */
    public function getYoutubeSecondLink(): ?string
    {
        return $this->youtubeSecondLink;
    }

    /**
     * @param string|null $youtubeSecondLink
     */
    public function setYoutubeSecondLink(?string $youtubeSecondLink): void
    {
        $this->youtubeSecondLink = $youtubeSecondLink;
    }

    /**
     * @return string|null
     */
    public function getSiteInternet(): ?string
    {
        return $this->siteInternet;
    }

    /**
     * @param string|null $siteInternet
     */
    public function setSiteInternet(?string $siteInternet): void
    {
        $this->siteInternet = $siteInternet;
    }

    /**
     * @return string|null
     */
    public function getTwitterLink(): ?string
    {
        return $this->twitterLink;
    }

    /**
     * @param string|null $twitterLink
     */
    public function setTwitterLink(?string $twitterLink): void
    {
        $this->twitterLink = $twitterLink;
    }

    /**
     * @return string|null
     */
    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    /**
     * @param string|null $origin
     */
    public function setOrigin(?string $origin): void
    {
        $this->origin = $origin;
    }

    /**
     * @return string|null
     */
    public function getThemeBackColor(): ?string
    {
        return $this->themeBackColor;
    }

    /**
     * @param string|null $themeBackColor
     */
    public function setThemeBackColor(?string $themeBackColor): void
    {
        $this->themeBackColor = $themeBackColor;
    }

    /**
     * @return string|null
     */
    public function getThemeTextColor(): ?string
    {
        return $this->themeTextColor;
    }

    /**
     * @param string|null $themeTextColor
     */
    public function setThemeTextColor(?string $themeTextColor): void
    {
        $this->themeTextColor = $themeTextColor;
    }

    /**
     * @return string|null
     */
    public function getStyleBackColor(): ?string
    {
        return $this->styleBackColor;
    }

    /**
     * @param string|null $styleBackColor
     */
    public function setStyleBackColor(?string $styleBackColor): void
    {
        $this->styleBackColor = $styleBackColor;
    }

    /**
     * @return string|null
     */
    public function getStyleTextColor(): ?string
    {
        return $this->styleTextColor;
    }

    /**
     * @param string|null $styleTextColor
     */
    public function setStyleTextColor(?string $styleTextColor): void
    {
        $this->styleTextColor = $styleTextColor;
    }

    /**
     * @return string|null
     */
    public function getStyle(): ?string
    {
        return $this->style;
    }

    /**
     * @param string|null $style
     */
    public function setStyle(?string $style): void
    {
        $this->style = $style;
    }




}



