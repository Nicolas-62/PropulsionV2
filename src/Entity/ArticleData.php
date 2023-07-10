<?php

namespace App\Entity;

use App\Entity\Traits\ExtraDataTrait;
use App\Entity\Traits\LanguageTrait;
use App\Entity\Traits\TimesTampableTrait;
use App\Repository\ArticleDataRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ArticleDataRepository::class)]
class ArticleData
{
    use TimestampableTrait;
    use ExtraDataTrait;


    #[ORM\ManyToOne(inversedBy: 'data')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $object = null;


}

