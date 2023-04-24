<?php

namespace App\Repository;

use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Mediaspec;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;


class CategoryRepository extends CMSRepository
{
    protected string $model_label        = 'category';
    protected string $model_alias        = 'c';
    protected string $model_key          = 'category_id';



    public function __construct(
        ManagerRegistry $registry,
    )
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * getArticles
     *
     * @param $category_id
     * @param bool $online
     * @return ArrayCollection
     */
    public function getArticles($category_id, $code_langue, bool $online = true): ArrayCollection
    {

        $articles = new ArrayCollection();

        $category = $this->findOneBy(['id'=> $category_id]);
        if($category != null) {
            $articles = $category->getArticles()->filter(function (Article $article) use ($code_langue, $online) {

                if ($online) {
                    return $article->isOnline($code_langue);
                } else {
                    return true;
                }
            });
        }
        return $articles;
    }


    /**
     * getMediaspecs
     * Récupère les mediaspecs qui s'applique à la catégorie passée en paramètre.
     * @param Category $entity
     * @return Mediaspec
     */
    public function getMediaspecs(Category $entity): array
    {
        $heritage  =   0;
        return $this->registry->getRepository(Mediaspec::class)->findByCategory($entity, $heritage);
    }
}
