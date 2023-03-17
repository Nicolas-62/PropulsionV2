<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ManagerRegistry;

Abstract class CMSRepository extends ServiceEntityRepository
{

    protected ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry, $class)
    {
        // Récupération du manager pour appel méthodes d'autres repositories.
        $this->registry = $registry;
        parent::__construct($registry, $class);
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

    /**
     * getParents
     * Récupère les éléments qui ne sont que des parents (il n'ont pas de parents, ce ne sont pas des enfants)
     * @return array
     */
    public function getParents(): array
    {
        $query = $this->createQueryBuilder($this->model_alias)
            ->where("$this->model_alias.$this->model_key IS NULL")
            ->orderBy("$this->model_alias.ordre", 'ASC')
            ->getQuery();
        // returns an array of Product objects
        return $query->getResult();
    }

    /**
     * @param $element_id
     * @param bool $online
     * @return ArrayCollection
     */
    public function getChildren($element_id, bool $online = true): ArrayCollection
    {

        $qb = $this->createQueryBuilder($this->model_alias);
        $qb->where("$this->model_alias.$this->model_key = :val");
        if($online) {
            $qb->join("$this->model_alias.online", 'online')
                ->andWhere('online.langue = 1')
                ->andWhere('online.online = 1');

        }
        $qb->orderBy("$this->model_alias.ordre", 'ASC')
            ->setParameter('val', $element_id);

        $query = $qb->getQuery();
        return new ArrayCollection($query->getResult());
    }


    /**
     * @param ArrayCollection $list
     * @param $element_id
     * @param bool $online
     * @return ArrayCollection
     */
    public function getGenealogy(ArrayCollection $list, $element_id, bool $online = true): ArrayCollection
    {
        // Element parent
        $element = $this->findOneBy(['id'=> $element_id]);
        $list->set('element', $element);
        // Enfants
        $sublist = new ArrayCollection();
        // Récup des catégories enfant
        $children = $this->getChildren($element_id, $online);
        // Si pas de categories enfant.
        if($children->isEmpty()) {
            // Si on traite des categories
            if($this->model_label == 'category') {
                // On récupère les articles enfant
                $articles = $this->getArticles($element_id, $online);
                foreach ($articles as $article) {
                    $childList = new ArrayCollection();
                    $sublist->add($this->registry->getRepository(Article::class)->getGenealogy($childList, $article->getId(), $online));
                }
            }
        }else{
            // Categories enfant
            foreach ($children as $child) {
                $childList = new ArrayCollection();
                $sublist->add($this->getGenealogy($childList, $child->getId(), $online));
            }
        }
        $list->set('elements', $sublist);
        return $list;
    }
}
