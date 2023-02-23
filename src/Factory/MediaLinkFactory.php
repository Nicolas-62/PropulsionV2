<?php

namespace App\Factory;

use App\Entity\MediaLink;
use App\Repository\MediaLinkRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<MediaLink>
 *
 * @method        MediaLink|Proxy create(array|callable $attributes = [])
 * @method static MediaLink|Proxy createOne(array $attributes = [])
 * @method static MediaLink|Proxy find(object|array|mixed $criteria)
 * @method static MediaLink|Proxy findOrCreate(array $attributes)
 * @method static MediaLink|Proxy first(string $sortedField = 'id')
 * @method static MediaLink|Proxy last(string $sortedField = 'id')
 * @method static MediaLink|Proxy random(array $attributes = [])
 * @method static MediaLink|Proxy randomOrCreate(array $attributes = [])
 * @method static MediaLinkRepository|RepositoryProxy repository()
 * @method static MediaLink[]|Proxy[] all()
 * @method static MediaLink[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static MediaLink[]|Proxy[] createSequence(array|callable $sequence)
 * @method static MediaLink[]|Proxy[] findBy(array $attributes)
 * @method static MediaLink[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static MediaLink[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class MediaLinkFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {

        return [
            'media' => MediaFactory::randomOrCreate(),
            'mediaspec' => MediaSpecFactory::randomOrCreate(),
            'article' => ArticleFactory::randomOrCreate(),
        ];

    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(MediaLink $mediaLink): void {})
        ;
    }

    protected static function getClass(): string
    {
        return MediaLink::class;
    }
}
