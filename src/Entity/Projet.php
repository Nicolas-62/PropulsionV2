<?php

namespace App\Entity;

use App\Entity\Traits\ErrorTrait;
use App\Entity\Traits\TimesTampableTrait;
use App\Repository\ProjetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
class Projet
{

    // Champs date.
    use TimestampableTrait;
    use ErrorTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $refInterne = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $RefExterne = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $client = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(nullable: true)]
    private ?int $montantHT = null;

    #[ORM\Column(nullable: true)]
    private ?int $montantTTC = null;

    #[ORM\Column(nullable: true)]
    private ?bool $etape1 = null;

    #[ORM\Column(nullable: true)]
    private ?bool $etape2 = null;

    #[ORM\Column(nullable: true)]
    private ?bool $etape3 = null;

    #[ORM\Column(nullable: true)]
    private ?int $UserEtape1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $userEtape2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $userEtape3 = null;


    public function __construct()
    {
        $this->created_at   = new \DateTimeImmutable();
        $this->updated_at   = new \DateTimeImmutable();
    }

    /**
     * Methode nessecaire pour appel des elements dans un sélecteur de formulaire.
     *
     * @return string
     */
    public function __toString(): string
    {
        if($this->refInterne) {
            return $this->refInterne;
        }else{
            return '';
        }
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefInterne(): ?string
    {
        return $this->refInterne;
    }

    public function setRefInterne(?string $refInterne): self
    {
        $this->refInterne = $refInterne;

        return $this;
    }

    public function getRefExterne(): ?string
    {
        return $this->RefExterne;
    }

    public function setRefExterne(?string $RefExterne): self
    {
        $this->RefExterne = $RefExterne;

        return $this;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(?string $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getMontantHT(): ?int
    {
        return $this->montantHT;
    }

    public function setMontantHT(?int $montantHT): self
    {
        $this->montantHT = $montantHT;

        return $this;
    }

    public function getMontantTTC(): ?int
    {
        return $this->montantTTC;
    }

    public function setMontantTTC(?int $montantTTC): self
    {
        $this->montantTTC = $montantTTC;

        return $this;
    }

    public function isEtape1(): ?bool
    {
        return $this->etape1;
    }

    public function setEtape1(?bool $etape1): self
    {
        $this->etape1 = $etape1;

        return $this;
    }

    public function isEtape2(): ?bool
    {
        return $this->etape2;
    }

    public function setEtape2(?bool $etape2): self
    {
        $this->etape2 = $etape2;

        return $this;
    }

    public function isEtape3(): ?bool
    {
        return $this->etape3;
    }

    public function setEtape3(?bool $etape3): self
    {
        $this->etape3 = $etape3;

        return $this;
    }

    public function isEtapeDisabled($etapeName, $userId, $isAdmin): ?bool
    {
        // Si un attribut portant ce nom existe
        if(property_exists($this, $etapeName)){
            // Si l'étape est cochée
            if($this->{$etapeName}){
                // Si l'étape suivante n'existe pas ou si elle n'est pas cochée
                if( ! property_exists($this, 'etape'.(intval(substr($etapeName, -1))+1)) ||
                    ! $this->{'etape'.(intval(substr($etapeName, -1))+1)}) {
                    // Si l'utilisateur courantest admin ou est celui qui a validé l'étape, on peut modifier le champ.
                    if ($isAdmin || $userId === $this->{'getUser' . $etapeName}()) {
                        return false;
                    } else {
                        return true;
                    }
                }else{
                    return true;
                }
            }
            // Si l'étape n'est pas cochée
            else{
                // Si l'étape précédente n'existe pas ou si elle est cochée
                if( ! property_exists($this, 'etape'.(intval(substr($etapeName, -1))-1)) ||
                    $this->{'etape'.(intval(substr($etapeName, -1))-1)}) {
                    return false;
                }else{
                    return true;
                }
            }
        }else{
            return false;
        }
    }

    public function getUserEtape1(): ?int
    {
        return $this->UserEtape1;
    }

    public function setUserEtape1(?int $UserEtape1): self
    {
        $this->UserEtape1 = $UserEtape1;

        return $this;
    }

    public function getUserEtape2(): ?int
    {
        return $this->userEtape2;
    }

    public function setUserEtape2(?int $userEtape2): self
    {
        $this->userEtape2 = $userEtape2;

        return $this;
    }

    public function getUserEtape3(): ?int
    {
        return $this->userEtape3;
    }

    public function setUserEtape3(?int $userEtape3): self
    {
        $this->userEtape3 = $userEtape3;

        return $this;
    }
}
