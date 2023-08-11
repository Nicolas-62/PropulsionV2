<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Language;
use App\Entity\Seo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

Abstract class CMSRepository extends ServiceEntityRepository
{
    protected $session;
    protected ManagerRegistry $registry;
    public function __construct(
        ManagerRegistry $registry,
        $class,
        RequestStack $requestStack,

    )
    {
        $this->registry = $registry;
        // Récupération du manager pour appel méthodes d'autres repositories.
        $this->session = $requestStack->getSession();
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
    public function getChildren($element_id, $code_langue, bool $online = true): ArrayCollection
    {
        $language = $this->registry->getRepository(Language::class)->findOneBy(['code' => $code_langue]);
        $qb = $this->createQueryBuilder($this->model_alias);
        $qb->where("$this->model_alias.$this->model_key = :val");


        $preview = $this->session->get('preview');

        if($online && $preview == null)
        {
          $qb->join("$this->model_alias.onlines", 'online')
                // Todo Passer la langue en paramètre
                ->andWhere('online.language = :language')
                ->andWhere('online.online = 1');
        }
        $qb->orderBy("$this->model_alias.ordre", 'ASC')
            ->setParameter('val', $element_id)
            ->setParameter('language', $language);

        $query = $qb->getQuery();
        return new ArrayCollection($query->getResult());
    }


    /**
     * @param ArrayCollection $list
     * @param $element_id
     * @param bool $online
     * @return ArrayCollection
     */
    public function getGenealogy(int $element_id, $code_langue, bool $online = true, string $field = 'ordre', string $order = 'DESC',  ArrayCollection $parent_list = null): ArrayCollection
    {
        // Si pas de liste parente, on en crée une.
        if($parent_list == null){
            $parent_list = new ArrayCollection();
        }
        // Element parent
        $element = $this->findOneBy(['id'=> $element_id]);
        $parent_list->set('element', $element);
        // Enfants
        $child_list = new ArrayCollection();
        // Récup des catégories enfant
        $children = $this->getChildren($element_id, $code_langue, $online);
        // Si pas d'enfants
        if($children->isEmpty()) {
            // Si on traite des categories
            if($this->model_label == 'category') {
                // On récupère les articles enfant
                $articles = $this->getArticles(array($element_id), $code_langue, $online, $field, $order);
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

    /**
     * vérifie sur la catégorie parent de l'article a la SEO d'activé
     *
     * @param Article|Category $entity
     * @return bool
     */
    public function hasSeo(Article|Category $entity): bool
    {
        // Si c'est une catégorie
        if($entity instanceof Category){
            return $entity->hasSeo();
        }
        // Sinon on cherche dans le ancêtres de l'article
        else {
            // On boucle sur les ancêtres de l'article
            foreach ($entity->getAncestors() as $parent) {
                // Si c'est une catégorie
                if ($parent instanceof Category) {
                    return $parent->hasSeo();
                }
            }
        }

        // retour
        return false;
    }

    /**
     * vérifie sur la catégorie parent de l'article a la SEO d'activé
     *
     * @param Article|Category $entity
     * @return Seo
     */
    public function getSeo(Article|Category $entity, $code_langue = null): Seo
    {
        $seo = $entity->getSeo($code_langue);
        if($seo == null){
            // On boucle sur les ancêtres de l'article
            foreach ($entity->getAncestors() as $parent) {
                // On retourne la Seo du premier ancetre qui en possède.
                if($parent->getSeo($code_langue) != null){
                    return $parent->getSeo($code_langue);
                }
            }
        }
        return $seo;
    }



}
