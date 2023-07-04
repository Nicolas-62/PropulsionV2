<?php

namespace App\Entity;

use App\Entity\Traits\CMSTrait;
use App\Entity\Traits\ExtraDataTrait;
use App\Entity\Traits\LanguageTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\PreviewTrait;
use App\Entity\Traits\TimesTampableTrait;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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
    use ExtraDataTrait;
    use MediaTrait;
    use LanguageTrait;

    // Champs supplémentaires
    private bool  $headline = FALSE;
    private bool  $vedette  = FALSE;
    private ?string $content  = '';
    private ?string $titleByLanguage  = '';



    // Liste des champs supplémentaires spécifiques.
    private array $extraFields = [
        ['name' => 'titleByLanguage', 'label' => "Titre", 'ea_type' => 'TextField'],
        ['name' => 'content', 'label' => "Contenu", 'ea_type' => 'TextEditorField'],
        ['name' => 'headline', 'label' => "Tête d'affiche", 'ea_type' => 'booleanField'],
        ['name' => 'vedette', 'label' => "Vedette", 'ea_type' => 'booleanField'],
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

    #[ORM\Column]
    private ?bool $hasSeo = null;


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
     * @return Collection<int, ArticleData>
     */
    public function getData(): Collection
    {
        return $this->data;
    }


    /**
     * @param $code_langue
     * @return void
     */
    public function getDatas($code_langue): void
    {
        $datas = $this->data->filter(function(ArticleData $data) use ($code_langue) {
            return $data->getLanguage()->getCode() === $code_langue;
        });
        foreach($datas as $data){
            $this->{'set' . $data->getFieldKey()}($data->getFieldValue());
        }
    }

    public function addData(ArticleData $data): self
    {
        if (!$this->data->contains($data)) {
            $this->data->add($data);
            $data->setObject($this);
        }

        return $this;
    }

    public function removeData(ArticleData $data): self
    {
        if ($this->data->removeElement($data)) {
            // set the owning side to null (unless already changed)
            if ($data->getObject() === $this) {
                $data->setObject(null);
            }
        }

        return $this;
    }


    public function getHeadline()
    {
        return $this->headline;
    }

    public function setHeadline($headline): void
    {
        $this->headline = $headline;
    }

    /**
     * @return bool
     */
    public function getVedette(): bool
    {
        return $this->vedette;
    }

    /**
     * @param bool $vedette
     */
    public function setVedette(bool $vedette): void
    {
        $this->vedette = $vedette;
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
     * @return Collection<int, Seo>
     */
    public function getSeo(): Collection
    {
        return $this->seo;
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



