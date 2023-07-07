<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\CategoryData;
use App\Entity\Language;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategoryData>
 *
 * @method CategoryData|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryData|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryData[]    findAll()
 * @method CategoryData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
abstract class ExtraDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, $class)
    {
        parent::__construct($registry, $class);
    }

    public function getDatas(Article|Category $publication, string $code_langue): array
    {
        $model_name = strtolower($publication->getClassName());


        // récupération de la langue.
        // TODO créer un service pour récupérer l'objet langue et le passer dans le constructeur
        $language = $this->getEntityManager()->getRepository(Language::class)->findOneBy(['code' => $code_langue]);
        // Récupération des datas de l'article dans la langue choisie.
        $datas = $this->createQueryBuilder('x')
            ->andWhere('x.object = :'.$model_name)
            ->andWhere('x.language = :language')
            ->setParameter($model_name, $publication)
            ->setParameter('language', $language)
            ->getQuery()
            ->getResult()
        ;
        // Formatage de la liste : key => object.
        $datas = array_reduce($datas, function(array $acc, $data){
            $acc[$data->getFieldKey()] = $data;
            return $acc;
        }, []);
        // Retourne la liste des champ supplémentaires de l'objet passé en paramètre, dans une langue donnée.
        return $datas;
    }




    public function save($entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove($entity, bool $flush = false): void
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
