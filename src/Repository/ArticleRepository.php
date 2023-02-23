<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function getArticles(string $object, int $object_id): array
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.object = :objet_nom')
            ->andWhere('a.object_id = :object_id')
            ->setParameter('object', $object)
            ->setParameter('object_id', $object_id)
            ->getQuery();

        // returns an array.
        return $query->getResult();
    }


    public function save(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getCategoryChildren($category_id, $online = true)
    {

        if ($online) {
            return $this->createQueryBuilder('a')
                ->andWhere('a.category = :val')
                ->setParameter('val', $category_id)
                ->join('a.online', 'online')
                ->andWhere('online.langue = 1')
                ->andWhere('online.online = 1')
                ->orderBy('a.ordre', 'ASC')
                ->getQuery()
                ->getResult();
        } else {
            return $this->createQueryBuilder('a')
                ->andWhere('a.category = :val')
                ->setParameter('val', $category_id)
                ->orderBy('a.ordre', 'ASC')
                ->getQuery()
                ->getResult();
        }
    }

    /**
     * @param ArrayCollection $list
     * @param $category_id
     * @param bool $online
     * @return ArrayCollection
     */
    public function getGenealogy(ArrayCollection $list, $article_id, bool $online = true): ArrayCollection
    {
        $article  = $this->findOneBy(['id'=> $article_id]);
        $list->set('element', $article);
        $list->set('elements', new ArrayCollection());

        return $list;
    }







//    /**
//     * @return Article[] Returns an array of Article objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Article
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
