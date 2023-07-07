<?php

namespace App\Entity;

use App\Entity\Traits\ExtraDataTrait;
use App\Entity\Traits\LanguageTrait;
use App\Entity\Traits\TimesTampableTrait;
use App\Repository\CategoryDataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryDataRepository::class)]
class CategoryData
{
    use TimestampableTrait;
    use ExtraDataTrait;


    #[ORM\ManyToOne(inversedBy: 'data')]
    #[ORM\JoinColumn(nullable: false)]
    private  ?Category $object = null;
}
