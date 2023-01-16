<?php

namespace App\Entity;

use App\Entity\Traits\CMSTrait;
use App\Entity\Traits\TimesTampableTrait;
use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    use CMSTrait;
    use TimestampableTrait;

    #[ORM\Column(length: 25)]
    private ?string $object = null;

    #[ORM\Column]
    private ?int $object_id = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;


    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(string $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getObjectId(): ?int
    {
        return $this->object_id;
    }

    public function setObjectId(int $object_id): self
    {
        $this->object_id = $object_id;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

}


