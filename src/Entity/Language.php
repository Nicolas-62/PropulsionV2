<?php

namespace App\Entity;

use App\Entity\Traits\TimesTampableTrait;
use App\Repository\LanguageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LanguageRepository::class)]
class Language
{

    // Champs date.
    use TimestampableTrait;


    public function __construct()
    {
        $this->online      = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
        $this->themes = new ArrayCollection();
    }


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column]
    private ?int $ordre = null;

    #[ORM\OneToMany(mappedBy: 'langue', targetEntity: Online::class)]
    private Collection $online;

    #[ORM\OneToMany(mappedBy: 'language', targetEntity: Theme::class)]
    private Collection $themes;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;

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
            $online->setLangue($this);
        }

        return $this;
    }

    public function removeOnline(Online $online): self
    {
        if ($this->online->removeElement($online)) {
            // set the owning side to null (unless already changed)
            if ($online->getLangue() === $this) {
                $online->setLangue(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if($this->label) {
            return $this->label;
        }else{
            return 'Français';
        }
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
            $theme->setLanguage($this);
        }

        return $this;
    }

    public function removeTheme(Theme $theme): self
    {
        if ($this->themes->removeElement($theme)) {
            // set the owning side to null (unless already changed)
            if ($theme->getLanguage() === $this) {
                $theme->setLanguage(null);
            }
        }

        return $this;
    }


}
