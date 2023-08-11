<?php

namespace App\Repository;

use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Mediaspec;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;


class CategoryRepository extends CMSRepository
{
    protected string $model_label        = 'category';
    protected string $model_alias        = 'c';
    protected string $model_key          = 'category_id';



    public function __construct(
        ManagerRegistry $registry,
        RequestStack $requestStack,
    )
    {
        parent::__construct($registry, Category::class, $requestStack);
    }

    /**
     * getArticles
     *
     * @param $category_id
     * @param $code_langue
     * @param bool $online
     * @param $field
     * @param $order
     * @return ArrayCollection
     */
    public function getArticles(array $category_ids, $code_langue, bool $online = true, string $field = 'ordre', string $order = 'DESC'): ArrayCollection
    {

        $articles = array();

        foreach($category_ids as $category_id) {
            $articlesByCategory = new ArrayCollection();
            $category = $this->findOneBy(['id' => $category_id]);

            if ($category != null) {
                // Récupération des articles de la catégorie.
                $articlesByCategory = $category->getArticles()->filter(function (Article $article) use ($code_langue, $online) {
                    $article->getDatas($code_langue);
                    $preview = $this->session->get('preview');
                    $returnFlag = true;
                    if ($online && $preview == null) {
                        $returnFlag = $article->isOnline($code_langue);
                    }
                    return $returnFlag;
                });
            }
            $articles = array_merge($articlesByCategory->toArray(), $articles);
        }

        $articles = new ArrayCollection($articles);
        // Trie des articles.
        $iterator = $articles->getIterator();
        $iterator->uasort(function ($a, $b) use ($field, $order) {
            if ($a->{'get' . ucfirst($field)}() == $b->{'get' . ucfirst($field)}()) {
                return 0;
            }
            if ($a->{'get' . ucfirst($field)}() < $b->{'get' . ucfirst($field)}()) {
                return ($order == 'DESC') ? 1 : -1;
            } else {
                return ($order == 'DESC') ? -1 : 1;
            }

        });
        $articles = new ArrayCollection(iterator_to_array($iterator));
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
