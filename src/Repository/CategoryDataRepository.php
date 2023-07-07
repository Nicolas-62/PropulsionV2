<?php

namespace App\Repository;


use App\Entity\CategoryData;
use Doctrine\Persistence\ManagerRegistry;


class CategoryDataRepository extends ExtraDataRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryData::class);
    }

}
