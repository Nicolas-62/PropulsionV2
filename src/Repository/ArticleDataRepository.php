<?php

namespace App\Repository;

use App\Entity\ArticleData;
use Doctrine\Persistence\ManagerRegistry;


class ArticleDataRepository extends ExtraDataRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleData::class);
    }
}
