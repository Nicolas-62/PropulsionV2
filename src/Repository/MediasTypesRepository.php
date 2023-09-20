<?php

namespace App\Repository;

use App\Entity\MediaType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @extends ServiceEntityRepository<MediaType>
 *
 * @method MediaType|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaType|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaType[]    findAll()
 * @method MediaType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediasTypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaType::class);
    }

    public function save(MediaType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MediaType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findOneByFiletype($filename): ?MediaType
    {
        $file  = new File($filename, false);
        return $this->createQueryBuilder('m')
            ->andWhere('m.filetype LIKE :extension')
            ->setParameter('extension', '%'.$file->getExtension().'%' )
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }


//    /**
//     * @return MediaType[] Returns an array of MediaType objects
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

//    public function findOneBySomeField($value): ?MediaType
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
