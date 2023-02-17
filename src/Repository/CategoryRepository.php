<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    private $registry = null;
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
        parent::__construct($registry, Category::class);
    }

    public function getParents(): array
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.category_id IS NULL')
            ->orderBy('c.position', 'ASC')
            ->getQuery();
        // returns an array of Product objects
        return $query->getResult();
    }

    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param $category_id
     * @param bool $online
     * @return ArrayCollection
     */
    public function getChildren($category_id, bool $online = true): ArrayCollection
    {

        $qb = $this->createQueryBuilder('c');
        $qb->where('c.category_id = :val');
        if($online) {
            $qb->join('c.online', 'online')
                ->andWhere('online.langue = 1')
                ->andWhere('online.online = 1');

        }
        $qb->orderBy('c.position', 'ASC')
            ->setParameter('val', $category_id);

        $query = $qb->getQuery();
        return new ArrayCollection($query->getResult());
    }

    /**
     * getArticles
     *
     * @param $category_id
     * @param bool $online
     * @return ArrayCollection
     */
    public function getArticles($category_id, bool $online = true): ArrayCollection
    {
        $qb = $this->registry->getRepository(Article::class)->createQueryBuilder('a');
        $qb->where('a.category = :val');
        if($online) {
            $qb->join('a.online', 'online')
                ->andWhere('online.langue = 1')
                ->andWhere('online.online = 1');
        }
        $qb->orderBy('a.position', 'ASC')
            ->setParameter('val', $category_id);

        $query = $qb->getQuery();
        return new ArrayCollection($query->getResult());
    }

    /**
     * @param ArrayCollection $list
     * @param $category_id
     * @param bool $online
     * @return ArrayCollection
     */
    public function getGenealogy(ArrayCollection $list, $category_id, bool $online = true)
    {
        // Element parent
        $category = $this->findOneBy(['id'=> $category_id]);
        $list->set('element', $category);
        // Enfants
        $list->set('elements', new ArrayCollection());

        $children = $this->getChildren($category_id, $online);
        if($children->isEmpty()) {
            // Articles
            $articles = $this->getArticles($category_id, $online);
            $sublist = new ArrayCollection();
            foreach ($articles as $article) {
                $childlist = new ArrayCollection();
                $sublist->add($this->registry->getRepository(Article::class)->getGenealogy($childlist, $article->getId(), $online));
            }
            $list->set('elements', $sublist);
            return $list;
        }else{
            // Categories
            $sublist = new ArrayCollection();
            foreach ($children as $child) {
                $childlist = new ArrayCollection();
                $sublist->add($this->getGenealogy($childlist, $child->getId(), $online));
            }
            $list->set('elements', $sublist);
            return $list;
        }
    }


//    /**
//     * @return Category[] Returns an array of Category objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
