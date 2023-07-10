<?php

namespace App\Entity\Traits;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Language;
use App\Entity\Media;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

// entrée unique pour chaque élément dans une langue donnée.
#[UniqueEntity(fields: ['object', 'field_key', 'language'])]
trait ExtraDataTrait
{
    // Champs date.
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $field_key = null;

    #[ORM\Column(type: Types::TEXT)]
    private $field_value = null;

    #[ORM\ManyToOne]
    private ?Language $language = null;

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

    public function getFieldValue(): ?string
    {
        return $this->field_value;
    }

    public function setFieldValue($field_value): self
    {
        $this->field_value = $field_value;

        return $this;
    }

    /**
     * @return Language|null
     */
    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    /**
     * @param Language|null $language
     */
    public function setLanguage(?Language $language): self
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return Category|Article|null
     */
    public function getObject(): Article|Category|null
    {
        return $this->object;
    }

    /**
     * @param Category|Article|null $object
     */
    public function setObject(Article|Category|null $object): self
    {
        $this->object = $object;
        return $this;
    }


}