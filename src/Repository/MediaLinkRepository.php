<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
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

    public function findOneByCategory(Category $category, Mediaspec $mediaspec): ?MediaLink
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.mediaspec = :mediaspec')
            ->andWhere('m.category   = :category')
            ->setParameter('mediaspec', $mediaspec)
            ->setParameter('category', $category)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
    /**
     * Retourne la liste des médias de type pdf que l'on peut associer à une entité (article/catégorie)
     *
     * @return array
     */
    public function getAllFilesForChoices(Article|Category $entity = null): array
    {
        $request = $this->createQueryBuilder('m');

        if(isset($entity)) {
            $request->andWhere('m.'.strtolower($entity->getClassName()).' = :entity');
            $request->setParameter('entity', $entity);
        }
        // On récupère les medias de type pdf

        $links = $request->join('m.media', 'media')
        ->join('media.mediaType', 'mediaType')
        ->andWhere('mediaType.label = :label')
        ->setParameter('label', 'pdf')
        ->getQuery()->getResult();
        dump($links);
        $datas = [];
        foreach($links as $link){
            $datas[$link->getMedia()->getMedia()] = $link->getId();
        }

        return $datas;
    }




    /**
     * Supprime les liens avec les medias de type fichier d'une entité
     *
     * @param Article|Category $entity
     * @return void
     */
    public function removeFileMediaLinks(Article|Category $entity): void
    {
        $mediaLinks = $this->findBy([strtolower($entity->getClassName()) => $entity]);
        foreach($mediaLinks as $mediaLink){
            if($mediaLink->getMedia()->getMediaType()->getLabel() == 'pdf'){
                $this->remove($mediaLink);
            }
        }
    }
}
