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

    public function __construct(
        ManagerRegistry $registry, $class)
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
            $qb->join("$this->model_alias.onlines", 'online')
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
    public function getGenealogy(int $element_id, $code_langue, bool $online = true, ArrayCollection $parent_list = null): ArrayCollection
    {
        if($parent_list == null){
            $parent_list = new ArrayCollection();
        }
        // Element parent
        $element = $this->findOneBy(['id'=> $element_id]);
        $parent_list->set('element', $element);
        // Enfants
        $child_list = new ArrayCollection();
        // Récup des catégories enfant
        $children = $this->getChildren($element_id, $online);
        // Si pas d'enfants
        if($children->isEmpty()) {
            // Si on traite des categories
            if($this->model_label == 'category') {
                // On récupère les articles enfant
                $articles = $this->getArticles($element_id, $code_langue, $online);
                foreach ($articles as $article) {
                    $child_list->add($this->registry->getRepository(Article::class)->getGenealogy($article->getId(), $code_langue, $online));
                }
            }
        }else{
            // Récupération des enfants
            foreach ($children as $child) {
                $child_list->add($this->getGenealogy($child->getId(), $code_langue, $online));
            }
        }
        // Ajout des enfants.
        $parent_list->set('elements', $child_list);
        return $parent_list;
    }
}
