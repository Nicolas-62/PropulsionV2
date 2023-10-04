<?php

namespace App\EventListener;

use App\Constants\Constants;
use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

// Ecouteur sur l'entité Média, gestion des images en cache gérées par le bundle Imagine.
// https://symfony.com/doc/current/doctrine/events.html#doctrine-entity-listeners
#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: Media::class)]
class ImageCacheListener {

    public function __construct(
        // Gestionnaire du cache
        private CacheManager $cacheManager,
        // Chemin des images en cache.
        private string $dyn_img_path
    )
    {
    }

    /**
     * preRemove
     * Fonction appelée avant la suppression d'un média.
     * @param Media $media
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function preRemove(Media $media, LifecycleEventArgs $event): void
    {
        // Suppression des vignettes du cache.
        $this->cacheManager->remove($this->dyn_img_path.$media->getMedia());
    }
}