<?php

namespace App\Entity\ExtraDataTrait;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

// Récupération des champs spécifiques à l'instance dans le csv associé.
$fields = array();
$filesystem = new Filesystem();
if($filesystem->exists(__DIR__.'/ArticleData.csv')){
    $file = new File(__DIR__.'/ArticleData.csv');
    $csvEncoder = new CsvEncoder();
    $fields = $csvEncoder->decode($file->getContent(), 'array');
}
define('ARTICLE_DATA_FIELDS', $fields);


trait ArticleDataTrait
{
    // Champs spécifiques !! à synchroniser manuellement avec les champs définis dans le csv
    // !! Ajouter les getters et les setters également, définir une valeur par défaut.
    private ?string $titleByLanguage  = '';
    private ?string $description  = '';
    private ?string $content          = '';
    private ?string $subtitle  = '';
    private ?\DateTimeImmutable $dateEvent  = null;
    private ?\DateTimeImmutable $heureEvent  = null;
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



    // Liste des champs supplémentaires spécifiques.
    private array $extraFields = ARTICLE_DATA_FIELDS;

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
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
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
     * @return bool|null
     */
    public function getReported(): ?bool
    {
        return $this->reported;
    }

    /**
     * @param bool|null $reported
     */
    public function setReported(?bool $reported): void
    {
        $this->reported = $reported;
    }

    /**
     * @return bool|null
     */
    public function getFull(): ?bool
    {
        return $this->full;
    }

    /**
     * @param bool|null $full
     */
    public function setFull(?bool $full): void
    {
        $this->full = $full;
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

    // Getters et Setters des champs spécifiques !! A mettre à jour
    public function getDateEvent(): \DateTimeImmutable
    {
        return new $this->dateEvent;
    }

    public function setDateEvent($dateEvent): void
    {
        if($dateEvent instanceof \DateTimeImmutable) {
            $this->dateEvent = $dateEvent;
        }
        else if(is_string($dateEvent)) {
            $this->dateEvent = new \DateTimeImmutable($dateEvent);
        }
    }

    public function getHeureEvent(): ?\DateTimeImmutable
    {
        return $this->heureEvent;
    }

    public function setHeureEvent($heureEvent): void
    {
        if($heureEvent instanceof \DateTimeImmutable) {
            $this->heureEvent = $heureEvent;
        }
        else if(is_string($heureEvent)) {
            $this->heureEvent = new \DateTimeImmutable($heureEvent);
        }
    }


}