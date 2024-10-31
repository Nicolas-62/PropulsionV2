<?php

namespace App\Entity;

use App\Entity\Traits\ErrorTrait;
use App\Repository\EleveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EleveRepository::class)]
class Eleve
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

    #[ORM\OneToMany(mappedBy: 'eleve', targetEntity: Note::class, orphanRemoval: true)]
    private Collection $notes;

    #[ORM\ManyToOne(inversedBy: 'eleves')]
    private ?Classe $classe = null;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    /**
     * @param $matiere
     * @return Note|null
     */
    public function getNote($matiere): ?Note
    {
        return $this->notes->findFirst(function (Note $note) use ($matiere) {
            return $note->getMatiere() === $matiere;
        });
    }


    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setEleve($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getEleve() === $this) {
                $note->setEleve(null);
            }
        }

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): static
    {
        $this->classe = $classe;

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

}
