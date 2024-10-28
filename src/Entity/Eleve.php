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


    /**
     * Fonction d'appel des proprités qui n'ont pas de getter/setter.
     * @param $name
     * @param $args
     * @return \DateTimeImmutable|void|null
     * @throws \Exception
     */
    public function __call($name, $args)
    {
        // Nom de la proprieté
        $property = lcfirst(substr($name, 3));
        if ('get' === substr($name, 0, 3)) {
            if(str_starts_with($property, 'note')) {
                $matiere_id = substr($property, -1);
                $valeur = $this->notes->findFirst(function ($key, Note $note) use ($matiere_id) {
                    return $note->getMatiere()->getId() == $matiere_id;
                })?->getValeur();
                return $valeur;
            }
            else if(str_starts_with($property, 'rate')) {
                $matiere_id = substr($property, -1);
                $valeur = $this->notes->findFirst(function ($key, Note $note) use ($matiere_id) {
                    return $note->getMatiere()->getId() == $matiere_id;
                })?->getRate();
                return $valeur;
            }
            return $this->{$property} ?? null;
        }
        elseif ('set' === substr($name, 0, 3)) {
            // Valeur de la proprieté
            $value = 1 == count($args) ? $args[0] : null;
            if(str_starts_with($property, 'note')) {
                $matiere_id = substr($property, -1);
                // Si la note existe déjà
                $note = $this->notes->findFirst(function ($key, Note $note) use ($matiere_id) {
                    return $note->getMatiere()->getId() == $matiere_id;
                });
                if($note != null){
                    $note->setValeur($value);
                }
            }
            if(str_starts_with($property, 'rate')) {
                $matiere_id = substr($property, -1);
                // Si la note existe déjà
                $note = $this->notes->findFirst(function ($key, Note $note) use ($matiere_id) {
                    return $note->getMatiere()->getId() == $matiere_id;
                });
                if($note != null){
                    $note->setRate($value);
                }
            }
            else{
                $this->{$property} = $value;
            }
        }
    }

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
