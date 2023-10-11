<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Media;
use App\Entity\MediaLink;
use App\Entity\Mediaspec;
use App\Entity\MediaType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @extends ServiceEntityRepository<Media>
 *
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    /**
     * Sauvegarde un média
     *
     * @param Media $entity
     * @param bool $flush
     * @return void
     */
    public function save(Media $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Récupère les photos d'un article de la galerie
     *
     * @param Article $entity
     * @return array
     */
    public function getPhotos(Article $entity): array
    {
        $query = $this->createQueryBuilder('m')
            ->join('m.mediaLinks', 'mediaLinks')
            ->where('m.section = :section')
            ->andWhere('mediaLinks.article = :article')
            ->setParameter('article', $entity)
            ->setParameter('section', 'galerie');
        return $query->getQuery()->getResult();
    }


    /**
     *  Supprime un média
     *
     * @param string $entityId
     * @param bool $flush
     * @return bool
     */
    public function removeById(string $entityId, bool $flush = false): bool
    {
        // Capteur
        $removed = true;
        // Récupération du media.
        $entity = $this->find($entityId);

        if( ! $entity){
            $removed = false;
        }
        // Si on a récupéré le média
        else {
            // On le supprime.
            $this->getEntityManager()->remove(
                $this->find($entityId)
            );
            // Si on met à jour la BDD
            if ($flush) {
                $this->getEntityManager()->flush();
            }
        }
        // retour
        return $removed;
    }
    /**
     * Supprime un média
     *
     * @param Media $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Media $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Retourne par type d'entité la liste des entitées liées au média
     *
     * @param Media $media
     * @return array|array[]
     */
    public function getRelatedEntities(Media $media){
        $media_links = $this->getEntityManager()->getRepository(MediaLink::class)->findBy(['media'=> $media]);
        $relatedEntities = array();
        foreach($media_links as $media_link){
            if($media_link->getArticle() != null){
                $relatedEntities['articles'][$media_link->getArticle()->getId()] = $media_link->getArticle()->getTitle();
            }
            if($media_link->getCategory() != null){
                $relatedEntities['categories'][$media_link->getCategory()->getId()] = $media_link->getCategory()->getTitle();
            }
        }
        return $relatedEntities;
    }

    /**
     * Retourne la liste des médias que l'on peut associer à une entité (article/catégorie)
     *
     * @return array
     */
    public function getAllForChoices(): array
    {
        $medias  =  $this->findAll();
        $choices =  array();
        foreach($medias as $media){
            $choices[$media->getMedia()] = $media->getId();
        }
        return $choices;
    }

    /**
     * Retourne la liste des médias de type pdf que l'on peut associer à une entité (article/catégorie)
     *
     * @return array
     */
    public function getAllFilesForChoices(): array
    {
        $medias  =  $this->findBy(['mediaType' => 2]);
        $files =  array();
        foreach($medias as $media){
            $files[$media->getMedia()] = $media->getId();
        }
        return $files;
    }

//    /**
//     * @return Media[] Returns an array of Media objects
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

//    public function findOneBySomeField($value): ?Media
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
