<?php

namespace App\Entity\ExtraDataTrait;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

// Récupération des champs spécifiques à l'instance dans le csv associé.
$fields = array();
$filesystem = new Filesystem();
if($filesystem->exists(__DIR__.'/CategoryData.csv')){
    $file = new File(__DIR__.'/CategoryData.csv');
    $csvEncoder = new CsvEncoder();
    $fields = $csvEncoder->decode($file->getContent(), 'array');
}
define('CATEGORY_DATA_FIELDS', $fields);


trait CategoryDataTrait
{
    // Champs spécifiques !! à synchroniser manuellement avec les champs définis dans le csv
    private ?string $titleByLanguage = '';
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

}