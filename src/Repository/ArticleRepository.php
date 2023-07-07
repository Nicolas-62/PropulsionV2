<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Media;
use App\Entity\MediaLink;
use App\Entity\Mediaspec;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
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

        // Recherche des mediaspec héritées du parent.
        $parent_mediaspecs      =   array();
        // Si l'article a un article parent.
        if($entity->getParent() != null) {
                $heritage++;
                // On récupère les mediaspecs du parent qui s'applique à l'enfant.
                $parent_mediaspecs   =   $mediaSpecRepository->findByHeritage($entity->getParent(), $heritage);
        }
        // On ajoute les mediaspecs à celles déjà récupérées.
        $mediaspecs   =   array_merge($current_mediaspecs, $parent_mediaspecs);

        // Recherche des mediaspec héritées de la catégorie parent.
        $category_mediaspecs    =   array();
        if($entity->getCategory() != null) {
            $heritage++;
            $category_mediaspecs    =   $mediaSpecRepository->findByCategory($entity->getCategory(), $heritage);
        }
        // On ajoute les mediaspecs à celles déjà récupérées.
        $mediaspecs   =  array_merge($mediaspecs, $category_mediaspecs);

        // retour
        return $mediaspecs;
    }

    /**
     * Récupère un média pour une médiapsec donnée
     *
     * @param Article $entity
     * @param Mediaspec $mediaspec_id
     * @return Media|null
     */
    public function getMedia(Article $entity, Mediaspec $mediaspec): Media|null
    {
        return $this->registry->getRepository(MediaLink::class)->findOneByArticle($entity, $mediaspec)?->getMedia();
    }

}
