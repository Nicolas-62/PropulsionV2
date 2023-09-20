<?php

namespace App\Entity;

use App\Entity\Traits\CMSTrait;
use App\Entity\Traits\ErrorTrait;
use App\Entity\Traits\ExtraDataTrait;
use App\Entity\Traits\ExtraFieldtrait;
use App\Entity\Traits\LanguageTrait;
use App\Entity\Traits\MediaTrait;
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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

// Récupération des champs spécifiques à l'instance dans le csv associé.
$fields = array();
$filesystem = new Filesystem();
if($filesystem->exists(__DIR__.'/ExtraFields/ArticleData.csv')){
    $file = new File(__DIR__.'/ExtraFields/ArticleData.csv');
    $csvEncoder = new CsvEncoder();
    $fields = $csvEncoder->decode($file->getContent(), 'array');
}
define('ARTICLE_DATA_FIELDS', $fields);

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    use CMSTrait;
    // Champs date.
    use TimestampableTrait;
    use ExtraFieldtrait;
    use MediaTrait;
    use LanguageTrait;
    use ErrorTrait;


    // Champs spécifiques !! à synchroniser manuellement avec les champs définis dans le csv
    // !! Ajouter les getters et les setters également, définir une valeur par défaut.
    private ?string $titleByLanguage = '';
    private ?string $description  = '';
    private ?string $content          = '';
    private ?string $subtitle  = '';
    private ?string $dateEvent;
    private ?string $datetimeEvent;
    private ?string $datetimeEndEvent;
    private ?string $youtubeLink  = '';
    private ?string $youtubeSecondLink  = '';
    private ?string $facebookLink  = '';
    private ?string $instagramLink  = '';
    private ?string $siteInternet  = '';
    private ?string $twitterLink  = '';
    private ?bool   $cancelled  = false;
    private ?bool   $reported  = false;
    private ?bool   $full  = false;
    private ?string $ticketingLink  = '';
    private ?string $typeMusic  = '';
    private ?string $origin  = '';
    private ?string $style              = '';
    private ?string $themeBackColor     = '#fa5faa';
    private ?string $themeTextColor     = '#FFFFFF';
    private ?string $styleBackColor     = '#000000';
    private ?string $styleTextColor     = '#FFFFFF';
    private ?bool   $star  = false;


    // Liste des champs supplémentaires spécifiques.
    private array $extraFields = ARTICLE_DATA_FIELDS;

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
        $this->parent       = null;
    }


    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
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


    /**
     * @var Collection|ArrayCollection
     */
    #[ORM\OneToMany(mappedBy: 'article', targetEntity: MediaLink::class, cascade: ['persist','remove'])]
    private Collection $mediaLinks;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Mediaspec::class)]
    private Collection $mediaspecs;

    #[ORM\OneToMany(mappedBy: 'object', targetEntity: ArticleData::class, orphanRemoval: true)]
    private Collection $data;

    #[ORM\ManyToMany(targetEntity: Theme::class, inversedBy: 'articles')]
    private Collection $themes;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Seo::class, cascade: ['persist','remove'])]
    private Collection $seo;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;


    public function getArticleId(): ?int
    {
        return $this->article_id;
    }

    public function setArticleId(?int $article_id): self
    {
        $this->article_id = $article_id;

        return $this;
    }

    /**
     * Ajout la seo uniquement si elle n'est pas vide.
     *
     * @param Seo $seo
     * @return $this
     */
    public function addSeo(Seo $seo): self
    {
        if (!$this->seo->contains($seo)) {
            if( ! $seo->isEmpty()){
                $this->seo->add($seo);
                $seo->setArticle($this);
            }
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
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
        $addLink = true;
        foreach($this->getMediaLinks() as $mediaLinked){
            // Si pour une mediaspec donnée on associe un nouveau media, on supprime l'ancien.
            if($mediaLinked->getMediaspec() != null){
                if($mediaLinked->getMediaspec()->getId() == $mediaLink ->getMediaspec()->getId()) {
                    $this->removeMediaLink($mediaLinked);
                }
            }else{
                // Si pour c'est un lien sans media spec
                if($mediaLinked->getMedia() != null){
                    // Si le lien exsite déjà, on ajoute pas le lien
                    if($mediaLinked->getMedia()->getId() == $mediaLink->getMedia()->getId()) {
                        $addLink = false;
                    }
                }
            }
        }
        if($addLink) {
            $this->mediaLinks->add($mediaLink);
            $mediaLink->setArticle($this);
        }
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

    /**
     * @param Collection $themes
     * @return Article
     */
    public function setThemes(Collection $themes): Article
    {
        $this->themes = $themes;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

}



