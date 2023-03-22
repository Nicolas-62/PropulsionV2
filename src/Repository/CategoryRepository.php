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



    public function __construct(ManagerRegistry $registry, String $locale)
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
    public function getArticles($category_id, bool $online = true, $code_langue = Constants::LOCALE): ArrayCollection
    {

        $category = $this->findOneBy(['id'=> $category_id]);
        $articles = $category->getArticles()->filter(function(Article $article) use ($online, $code_langue) {

            if($online) {
                return $article->isOnline($code_langue);
            }else{
                return true;
            }
        });
        return $articles;

//        $qb = $this->registry->getRepository(Article::class)->createQueryBuilder('a');
//        $qb->where('a.category = :val');
//        if($online) {
//            $qb->join('a.online', 'online')
//                ->andWhere('online.langue = 1')
//                ->andWhere('online.online = 1');
//        }
//        $qb->orderBy('a.ordre', 'ASC')
//            ->setParameter('val', $category_id);
//
//        $query = $qb->getQuery();
//        return new ArrayCollection($query->getResult());
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
