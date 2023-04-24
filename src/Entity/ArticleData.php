<?php

namespace App\Entity;

use App\Entity\Traits\TimesTampableTrait;
use App\Repository\ArticleDataRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ArticleDataRepository::class)]
// entrée unique pour chaque élément dans une langue donnée.
#[UniqueEntity(fields: ['object', 'field_key', 'language'])]
class ArticleData
{
    // Champs date.
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $field_key = null;

    #[ORM\Column(type: Types::BLOB)]
    private $field_value = null;

    #[ORM\ManyToOne]
    private ?Language $language = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'data')]
    #[ORM\JoinColumn(nullable: false)]
    private ?article $object = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFieldKey(): ?string
    {
        return $this->field_key;
    }

    public function setFieldKey(string $field_key): self
    {
        $this->field_key = $field_key;

        return $this;
    }

    public function getFieldValue()
    {
        return $this->field_value;
    }

    public function setFieldValue($field_value): self
    {
        $this->field_value = $field_value;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getObject(): ?article
    {
        return $this->object;
    }

    public function setObject(?article $object): self
    {
        $this->object = $object;

        return $this;
    }
}
