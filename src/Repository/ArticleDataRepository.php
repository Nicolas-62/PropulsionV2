<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\ArticleData;
use App\Entity\Language;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ArticleData>
 *
 * @method ArticleData|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleData|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleData[]    findAll()
 * @method ArticleData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleData::class);
    }

    public function getDatas(Article $article, string $code_langue): array
    {
        // récupération de la langue.
        // TODO créer un service pour récupérer l'objet langue et le passer dans le constructeur
        $language = $this->getEntityManager()->getRepository(Language::class)->findOneBy(['code' => $code_langue]);
        // Récupération des dats de l'article dans la langue choisie.
        $datas = $this->createQueryBuilder('a')
            ->andWhere('a.object = :article')
            ->andWhere('a.language = :language')
            ->setParameter('article', $article)
            ->setParameter('language', $language)
            ->getQuery()
            ->getResult()
        ;
        // Formatage de la liste : key => object.
        $datas = array_reduce($datas, function(array $acc, ArticleData $data){
            $acc[$data->getFieldKey()] = $data;
            return $acc;
        }, []);
        // Retourne la liste des champ supplémentaires de l'objet passé en paramètre, dans une langue donnée.
        return $datas;
    }




    public function save(ArticleData $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ArticleData $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ArticleData[] Returns an array of ArticleData objects
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

//    public function findOneBySomeField($value): ?ArticleData
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
