<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Media;
use App\Entity\MediaLink;
use App\Entity\Mediaspec;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\HttpFoundation\RequestStack;


class ArticleRepository extends CMSRepository
{
    protected string $model_label        = 'article';
    protected string $model_alias        = 'a';
    protected string $model_key          = 'article_id';


    public function __construct(ManagerRegistry $registry, RequestStack $requestStack)
    {
        parent::__construct($registry, Article::class,$requestStack);
    }

    /**
     * Récupère les mediaspecs qui s'applique à l'article passé en paramètre.
     *
     * @param Article $entity
     * @return Mediaspec
     */
    public function getMediaspecs(Article $entity): array
    {
        $heritage               =   0;
        $mediaSpecRepository    =   $this->registry->getRepository(Mediaspec::class);
        // Récupération des mediaspec définies spécialement pour l'objet.
        $current_mediaspecs     =   $mediaSpecRepository->findByHeritage($entity, $heritage);
        $mediaspecs             =   array();
        // Si l'article a un article parent.
        if($entity->getParent() != null) {
                $heritage++;
                // On récupère les mediaspecs du parent qui s'applique à l'enfant.
                $parent_mediaspecs   =   $mediaSpecRepository->findByHeritage($entity->getParent(), $heritage);
                // On ajoute les mediaspecs à celles déjà récupérées.
                $mediaspecs   =   array_merge($current_mediaspecs, $parent_mediaspecs);

            // Recherche des mediaspecs héritées de la catégorie parent de l'article parent.
            if($entity->getParent()->getCategory() != null){
                $heritage++;
                $category_mediaspecs = $mediaSpecRepository->findByCategory($entity->getParent()->getCategory(), $heritage);
                // On ajoute les mediaspecs à celles déjà récupérées.
                $mediaspecs = array_merge($mediaspecs, $category_mediaspecs);
            }
        }
        // Si l'article a une catégorie parent
        else {
            // Recherche des mediaspec héritées de la catégorie parent.
            if ($entity->getCategory() != null) {
                $heritage++;
                $category_mediaspecs = $mediaSpecRepository->findByCategory($entity->getCategory(), $heritage);
                // On ajoute les mediaspecs à celles déjà récupérées.
                $mediaspecs = array_merge($mediaspecs, $category_mediaspecs);
            }
        }

        // retour
        return $mediaspecs;
    }


    /**
     * Récupère les articles dont la catégorie parent autorise la création de sous articles.
     *
     * @param $code_langue
     * @return ArrayCollection
     */
    public function getHasSubArticleArticles(?Article $article, $code_langue): ArrayCollection
    {
        // Récupération des articles
        $request = $this->createQueryBuilder('a');
        $request->orderBy('a.title', 'ASC');
        $articles = new ArrayCollection($request->getQuery()->getResult());
        // Filtre des articles
        return $articles->filter(function($article_choice) use ($article, $code_langue){
            if($article != null && $article_choice->getId() == $article->getId()) {
                return false;
            }
            // Récupération de la catégorie parent
            $category = $article_choice->getCategoryParent();

            if ($category != null) {
                $category->getDatas($code_langue);
                if ($category->getHasSubArticle()) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        });
    }

    /**
     * Duplique un article
     * @param Article $article
     * @return Article
     */
    public function clone(Article $article){
        // Copie de l'objet
        $new_article = clone $article;
        // Suppression de l'id
        $new_article->setId(null);
        // Mise à jour des dates
        $new_article->setCreatedAt(new \DateTimeImmutable());
        $new_article->setUpdatedAt(new \DateTimeImmutable());
        // Copie des datas
        foreach($article->data as $data){
            $new_data = clone $data;
            $new_data->setObject($new_article);
            $this->save($new_data);
        }
        // Copie de la SEO
        $seo = $article->getSeo();
        if($seo){
            $new_seo = clone $seo;
            $new_seo->setArticle($new_article);
            $this->save($new_seo);

        }
        // Sauvegarde de l'article et des objets associés.
        $this->save($new_article, true);
        return $new_article;

    }

    /**
     * Récupère un média pour une médiapsec donnée
     *
     * @param Article $entity
     * @param Mediaspec $mediaspec_id
     * @return Media|null
     */
    public function getMedia(Article $entity, Mediaspec $mediaspec = null): Media|null
    {

        return $this->registry->getRepository(MediaLink::class)->findOneByEntity($entity, $mediaspec)?->getMedia();
    }


    /**
     * Récupère l'article ayant le même slug et le même parent ou la même catégorie
     *
     * @param Article $entity
     * @return Article|null
     * @throws NonUniqueResultException
     */
    public function getArticleWithSameSlug(Article $entity): Article|null
    {
        // On récupère l'article ayant le même parent et le même slug
        $query = $this->createQueryBuilder('a')->andWhere('a.slug = :slug');

        if($entity->getId() != null) {
             $query->andWhere('a.id != :id')->setParameter('id', $entity->getId());
        }
        $query->andWhere('a.id != :id')
            ->setParameter('slug', $entity->getSlug())
            ->setParameter('id', $entity->getId())
            ->setMaxResults(1);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Modifie l'ordre d'un article
     * @param Article $entity
     * @param string $direction
     * @return int
     */
    public function setOrdre(Article $entity, string $direction): int
    {
        if($direction == 'up'){
            if($entity->getOrdre() > 0) {
                $entity->setOrdre($entity->getOrdre() - 1);
                // On met à jour l'entité qui possède le même ordre, en lui ajoutant 1
                $this->createQueryBuilder('a')
                    ->update()
                    ->set('a.ordre', 'a.ordre + 1')
                    ->andWhere('a.ordre = :ordre')
                    ->andWhere('a.category = :category')
                    ->setParameter('ordre', $entity->getOrdre())
                    ->setParameter('category', $entity->getCategory())
                    ->getQuery()
                    ->execute();
                // On persiste l'entité
                $this->getEntityManager()->persist($entity);
                $this->getEntityManager()->flush();
            }

        }else if($direction == 'down'){
            // On recherche l'entité qui possède l'ordre suivant
            $next_entity = $this->createQueryBuilder('a')
                ->andWhere('a.ordre = :ordre')
                ->andWhere('a.category = :category')
                ->setParameter('ordre', $entity->getOrdre() + 1)
                ->setParameter('category', $entity->getCategory())
                ->getQuery()
                ->getOneOrNullResult();
            // Si on a trouvé une entité
            if($next_entity != null) {
                // On met à jour l'ordre de l'entité
                $entity->setOrdre($entity->getOrdre() + 1);

                // On met à jour l'entité qui possède le même ordre, en lui enlevant 1
                $this->createQueryBuilder('a')
                    ->update()
                    ->set('a.ordre', 'a.ordre - 1')
                    ->andWhere('a.ordre = :ordre')
                    ->andWhere('a.category = :category')
                    ->setParameter('ordre', $entity->getOrdre())
                    ->setParameter('category', $entity->getCategory())
                    ->getQuery()
                    ->execute();
                // On persiste l'entité
                $this->getEntityManager()->persist($entity);
                $this->getEntityManager()->flush();
            }
        }

        return $entity->getOrdre();
    }


    /**
     *  Met un media lié à l'article en vedette, dévedettise les autres.
     *
     * @param string $entityId
     * @param bool $flush
     * @return bool
     */
    public function starMedia(string $entityId, string $mediaId): bool
    {
        // Capteur
        $stared = false;
        // Récupération de l'article.
        $entity = $this->find($entityId);

        if( ! $entity){
            $stared = false;
        }
        // Si on a récupéré l'article
        else {
            // On passe tous ses medias liés en non vedette
            foreach($entity->getMediaLinks() as $mediaLink){
                // Si le media possède le même id que celui passé en paramètre
                if($mediaLink->getMedia()->getId() == $mediaId){
                    // On le passe en vedette.
                    $mediaLink->getMedia()->setStar(true);
                    $stared = true;
                }else {
                    $mediaLink->getMedia()->setStar(false);
                }
            }
            // Si on met à jour la BDD
            $this->getEntityManager()->flush();
        }
        // retour
        return $stared;
    }


}
