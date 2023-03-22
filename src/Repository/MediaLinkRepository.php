<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\MediaLink;
use App\Entity\Mediaspec;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MediaLink>
 *
 * @method MediaLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaLink[]    findAll()
 * @method MediaLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaLink::class);
    }

    public function save(MediaLink $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MediaLink $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return MediaLink[] Returns an array of MediaLink objects
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

    public function findOneByArticle(Article $article, Mediaspec $mediaspec): ?MediaLink
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.mediaspec = :mediaspec')
            ->andWhere('m.article   = :article')
            ->setParameter('mediaspec', $mediaspec)
            ->setParameter('article', $article)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
