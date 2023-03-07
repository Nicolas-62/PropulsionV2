<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Mediaspec;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mediaspec>
 *
 * @method Mediaspec|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mediaspec|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mediaspec[]    findAll()
 * @method Mediaspec[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaspecsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mediaspec::class);
    }

    public function save(Mediaspec $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Mediaspec $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * findByHeritage
     * Récupère les mediaspecs de l'élément passé en paramètre
     * @param Article|Category $entity
     * @param int $heritage
     * @return array
     */
    public function findByHeritage(Article|Category $entity, int $heritage): array
    {
        $model_name = strtolower($entity->getClassName());

        return $this->createQueryBuilder('m')
            ->andWhere("m.$model_name = :entity")
            ->andWhere("m.heritage = :level")
            ->setParameter('entity', $entity)
            ->setParameter('level', $heritage)
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * findByCategory
     * Récupère les mediaspecs de la catégorie passé en paramètre et celles des parents de la catégorie.
     * @param Category $category
     * @param int $heritage
     * @return array
     */
    public function findByCategory(Category $category, int $heritage): array
    {

        $current_mediaspecs   =   $this->findByHeritage($category, $heritage);
        $parent_mediaspecs    =   array();
        if($category->getParent() != null){
            $heritage++;
            $parent_mediaspecs = $this->getMediaspecsFromCategories($category->getParent(), $heritage);
        }
        $mediaspecs = array_merge($current_mediaspecs, $parent_mediaspecs);
        return $mediaspecs;
    }

//    /**
//     * @return Mediaspec[] Returns an array of Mediaspec objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Mediaspec
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
