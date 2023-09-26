<?php

namespace App\EventListener;

use App\Entity\Article;
use App\Entity\ArticleData;
use App\Entity\Category;
use App\Entity\Media;
use App\Entity\MediaLink;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatableMessage;

class EntityListener implements EventSubscriberInterface
{
    private SessionInterface $session;
    private AdminContextProvider $context;
    private EntityManagerInterface $entityManager;

    public function __construct(
        AdminContextProvider $contextProvider,
        RequestStack $request,
        // Gestionnaire d'entité
        EntityManagerInterface $entityManager
    )
    {
        // Gestionnaire d'entité
        $this->entityManager = $entityManager;
        $this->context = $contextProvider;
        $this->session = $request->getSession();
    }

    /**
     * permet de mettre en place les listeners souhaités que l'on retrouve dans ce fichier
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        // On défini le parent avant de définir le slug
        return [
            BeforeEntityUpdatedEvent::class => [['defineParent', 1], ['defineSLug', 0], ['linkFiles', 0],['defineStar', 0]],
            BeforeEntityPersistedEvent::class => [['defineParent', 10], ['defineSLug', 9],['generateHash',8],['defineStar', 7]],


        ];
    }

    /**
     * Controle le choix d'un article parent ou d'une catégorie
     *
     * @param BeforeEntityUpdatedEvent|BeforeEntityPersistedEvent $event
     * @return void
     */
    public function defineSLug(BeforeEntityUpdatedEvent|BeforeEntityPersistedEvent $event): void
    {
        // Récupération de l'entité.
        $entity = $event->getEntityInstance();
        // Si l'entité est une catégorie ou un article.
        if ($entity instanceof Article) {
            // Création du slug
            $slugger = new Slugify();
            $entity->setSlug($slugger->slugify($entity->getTitle()));
            // Vérification de l'unicité du slug
            $slug = $entity->getSlug();
            // On récupère les articles qui possèdent le même slug avec un numéro à la fin.
            $article = $this->entityManager->getRepository(Article::class)->getArticleWithSameSlug($entity);
            // Si un article possède déjà ce slug.
            if($article != null){
                // On tente d'ajouter un numéro à la fin du slug.
                foreach(range(1, 3) as $number) {
                    // On ajoute un caractère à la fin du slug.
                    $entity->setSlug($slug.'-'.$number);
                    // On récupère l'article qui possède le même slug.
                    $article = $this->entityManager->getRepository(Article::class)->getArticleWithSameSlug($entity);
                    // Si aucun article ne possède ce slug.
                    if($article == null){
                        // On sort de la boucle.
                        break;
                    }else{
                        // Si c'est le dernier tour de boucle.
                        if($number == 3){
                            // On génère une erreur.
                            $this->session->getFlashBag()->add('danger', new TranslatableMessage('content_admin.flash_message.error.slug.unique', [
                                '%slug%' => ($entity->getSlug()),
                                '%number%' => $number,
                            ], 'messages'));
                            // On indique que l'entité n'est pas valide.
                            $entity->setError();
                        }
                    }
                }
            }
        }
    }


    /**
     * Associe les fichiers à l'entité
     *
     * @return void
     */
    public function linkFiles(BeforeEntityUpdatedEvent $event): void
    {
        // Récupération de l'entité.
        $entity = $event->getEntityInstance();

        // Si l'entité est un article ou une catégorie
        if ($entity instanceof Article || $entity instanceof Category) {
            // Si l'entité n'est pas en erreur.
            if (!$entity->hasError()) {
                // On supprime les liens avec des fichiers
                $this->entityManager->getRepository(MediaLink::class)->removeFileMediaLinks($entity);
                // Récupération des fichiers choisis dans le formulaire multiple
                // Pour chaque fichier
                foreach ($entity->getMediaLinks() as $mediaLink) {
                    $mediaLink->{'set'.$entity->getClassName()}($entity);
                }
            }
        }
    }

    /**
     * Controle le choix d'un article parent ou d'une catégorie
     *
     * @param BeforeEntityUpdatedEvent|BeforeEntityPersistedEvent $event
     * @return void
     */
    public function defineParent(BeforeEntityUpdatedEvent|BeforeEntityPersistedEvent $event): void
    {
        // Récupération de l'entité.
        $entity = $event->getEntityInstance();

        // Si l'entité est une catégorie ou un article.
        if ($entity instanceof Article) {
            // Temoin choix valide d'un parent.
            $validChoice  = true;
            // Si l'entité a un parent.
            if ($entity->getParent() != null) {
                // Si l'entité a une catégorie.
                if($entity->getCategory() != null){
                    $validChoice = false;
                }
            }
            // Si l'entité ne possède pas de parent.
            else{
                // Si l'entité n'a pas de catégorie.
                if($entity->getCategory() == null){
                    $validChoice = false;
                }
            }
            // Si le choix n'est pas valide.
            if(!$validChoice) {

                // Les messages flash ne sont pas générés pour les requêtes AJAX
                if (!$this->context->getContext()->getRequest()->isXmlHttpRequest()) {
                    $this->session->getFlashBag()->add('danger', new TranslatableMessage('content_admin.flash_message.error.parent.choice', [
                        '%name%' => (string)$event->getEntityInstance(),
                    ], 'messages'));
                }
            }
        }
    }


    /**
     * @param BeforeEntityPersistedEvent $event
     * @return void
     * @description Permet de générer un hash pour les articles et les catégories
     */
    public function generateHash(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        // Si l'entité est un article ou une catégorie
        if ($entity instanceof Article || $entity instanceof Category) {

            $hash = $entity->generateHash();
            $entity->setHash($hash);

            // Si l'entité n'est pas en erreur.
            if (!$entity->hasError()) {

            }
        }
    }

    /**
     * @param BeforeEntityUpdatedEvent|BeforeEntityPersistedEvent $event
     * @return void
     * @description Permet de définir l'article vedette et de supprimer les autres articles vedettes
     */
    public function defineStar(BeforeEntityUpdatedEvent|BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        // Si l'entité est un article ou une catégorie
        if ($entity instanceof Article) {
            if($entity->getStar()){
                $vedette = $this->entityManager->getRepository(ArticleData::class)->findBy(['field_key' => 'star','field_value' => true]);
                $article_ids = [];

                foreach($vedette as $vedette){
                    $article_ids[] = $vedette->getObject()->getId();
                }

                foreach ($article_ids as $article_id){
                    if($entity->getId() != $article_id)
                    $article_data = $this->entityManager->getRepository(ArticleData::class)->findOneBy(['object' => $article_id,'field_key' => 'star','field_value' => true]);
                    $article_data->setFieldValue(false);

                }


                $entity->setStar(true);

            }

            // Si l'entité n'est pas en erreur.
            if (!$entity->hasError()) {

            }
        }
    }




}