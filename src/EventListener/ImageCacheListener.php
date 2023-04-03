<?php

namespace App\EventListener;

use App\Constants\Constants;
use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

// Ecouteur sur l'entité Média
// https://symfony.com/doc/current/doctrine/events.html#doctrine-entity-listeners
#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: Media::class)]
class ImageCacheListener {
    // Gestionnaire du cache
    private CacheManager $cacheManager;

    public function __construct(CacheManager $cacheManager, UploaderHelper $helper){
        $this->cacheManager = $cacheManager;
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
        $this->cacheManager->remove(Constants::ASSETS_IMG_PATH.$media->getMedia());
    }
}