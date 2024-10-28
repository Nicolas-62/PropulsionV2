<?php

namespace App\Entity;

use App\Entity\Traits\ErrorTrait;
use App\Repository\ProfesseurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfesseurRepository::class)]
class Professeur
{
    use ErrorTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 20)]
    private ?string $sexe = null;

    #[ORM\OneToMany(mappedBy: 'professeur', targetEntity: Classe::class)]
    private Collection $classes;

    #[ORM\ManyToOne(inversedBy: 'professeurs')]
    private ?Matiere $matiere = null;


    public function __construct()
    {
        $this->classes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * @return Collection<int, Classe>
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClasse(Classe $classe): self
    {
        if (!$this->classes->contains($classe)) {
            $this->classes->add($classe);
            $classe->setProfesseur($this);
        }

        return $this;
    }

    public function removeClasse(Classe $classe): self
    {
        if ($this->classes->removeElement($classe)) {
            // set the owning side to null (unless already changed)
            if ($classe->getProfesseur() === $this) {
                $classe->setProfesseur(null);
            }
        }

        return $this;
    }

    /**
     * Methode nessecaire pour appel des elements dans un sélecteur de formulaire.
     *
     * @return string
     */
    public function __toString(): string
    {
        if($this->nom && $this->prenom){
            return $this->prenom." ".$this->nom;
        }else{
            return '';
        }
    }

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): self
    {
        $this->matiere = $matiere;

        return $this;
    }

    public function setClasses(Collection $classes): Professeur
    {
        $this->classes = $classes;
        foreach ($classes as $classe) {
            $classe->setProfesseur($this);
        }
        return $this;
    }
}
