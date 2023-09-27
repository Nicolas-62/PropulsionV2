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



    public function findOneByEntity(Category|Article $entity, Mediaspec $mediaspec = null): ?MediaLink
    {
        if($entity instanceof Article) {
            return $this->createQueryBuilder('m')
              ->andWhere('m.mediaspec = :mediaspec')
              ->andWhere('m.article  = :article')
              ->setParameter('mediaspec', $mediaspec)
              ->setParameter('article', $entity)
              ->getQuery()
              ->getOneOrNullResult();
        }else{
            return $this->createQueryBuilder('m')
              ->andWhere('m.mediaspec = :mediaspec')
              ->andWhere('m.entity   = :entity')
              ->setParameter('mediaspec', $mediaspec)
              ->setParameter('category', $entity)
              ->getQuery()
              ->getOneOrNullResult()
              ;
        }


    }


    /**
     * Retourne la liste des médias de type pdf que l'on peut associer à une entité (article/catégorie)
     *
     * @return array
     */
    public function getFilesByEntityForChoices(Article|Category $entity ): array
    {
        $request = $this->createQueryBuilder('m');
        $request
            // Filtre sur l'entité
            ->andWhere('m.'.strtolower($entity->getClassName()).' = :entity')
            ->setParameter('entity', $entity)
            // Filtre sur le type de média
            ->join('m.media', 'media')
            ->join('media.mediaType', 'mediaType')
            ->andWhere('mediaType.label = :label')
            ->setParameter('label', 'pdf');
        // Récupération des liens
        $links = $request->getQuery()->getResult();
        $datas = [];
        // Formatage des données pour le sélecteur html, format : label => id
        foreach($links as $link){
            $datas[$link->getMedia()->getMedia()] = $link->getMedia()->getId();
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
        // Récupération des liens entre l'entité et les medias
        $mediaLinks = $this->findBy([strtolower($entity->getClassName()) => $entity]);
        // pour chaque lien
        foreach($mediaLinks as $mediaLink){
            // Si le media est de type fichier
            if($mediaLink->getMedia()->getMediaType()->getLabel() == 'pdf'){
                // Suppression du lien
                $this->remove($mediaLink);
            }
        }
    }
}
