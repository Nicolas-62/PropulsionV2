<?php

namespace App\Repository;

use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Media;
use App\Entity\MediaLink;
use App\Entity\Mediaspec;
use App\Entity\User;
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
     * @param $limit
     * @return ArrayCollection
     */
    public function getArticles(array $category_ids, $code_langue, bool $online = true, string $field = 'ordre', string $order = 'DESC', $limit = null): ArrayCollection
    {

        $articles = array();

        foreach($category_ids as $category_id) {
            $articlesByCategory = new ArrayCollection();
            $category = $this->findOneBy(['id' => $category_id]);

            if ($category != null) {
                // Récupération des articles de la catégorie.
                $articlesByCategory = $category->getArticles()->filter(function (Article $article) use ($code_langue, $online) {
                    // Récupération des datas de l'article.
                    $article->getDatas($code_langue);
                    // Récupération de l'article en ligne ou en mode preview.
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
        // On transforme le tableau en ArrayCollection.
        $articles = new ArrayCollection($articles);

        // Si on a précisé une limite, on la prend en compte.
        if($limit){
            $articles = new ArrayCollection($articles->slice(0, $limit));
        }

        // Trie des articles par l'ordre passé en paramètre.
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


    /**
     * Récupère les catégories qui auxquelles l'utilisateur peut ajouter/enlever des articles.
     *
     * @param User $user
     * @return Category[]
     */
    public function getHasCreateCategories(User $user, string $code_langue): array
    {
        $request = $this->createQueryBuilder('c');
        // Seul les développeurs sont autorisés à ajouter des articles à des catégories bloquées en ajout/suppression d'articles
        if( ! $user->hasRole('ROLE_DEV')) {
            $request->andWhere('data.field_key = :fieldKey');
            $request->setParameter('fieldKey', 'hasCreate');
            $request->andWhere('data.field_value = :fieldValue');
            $request->setParameter('fieldValue', true);
            $request->join('c.data', 'data');
            // On filtre sur la langue
            $request->andWhere('language.code = :languageCode');
            $request->setParameter('languageCode', $code_langue);
            $request->join('data.language', 'language');
        }
        $request->orderBy('c.title', 'ASC');
        return $request->getQuery()->getResult();
    }

    public function hasCreate($category, string $code_langue): ?Category
    {
        $request = $this->createQueryBuilder('c');
        $request->andWhere('c = :category');
        $request->setParameter('category', $category);
        $request->andWhere('data.field_key = :fieldKey');
        $request->setParameter('fieldKey', 'hasCreate');
        $request->andWhere('data.field_value = :fieldValue');
        $request->setParameter('fieldValue', true);
        $request->join('c.data', 'data');
        // On filtre sur la langue
        $request->andWhere('language.code = :languageCode');
        $request->setParameter('languageCode', $code_langue);
        $request->join('data.language', 'language');

        return $request->getQuery()->getSingleResult();
    }

    /**
     * Récupère un média pour une médiapsec donnée
     *
     * @param Category $entity
     * @param Mediaspec $mediaspec_id
     * @return Media|null
     */
    public function getMedia(Category $entity, Mediaspec $mediaspec = null): Media|null
    {
        return $this->registry->getRepository(MediaLink::class)->findOneByEntity($entity, $mediaspec)?->getMedia();
    }
}
