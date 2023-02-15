<?php

namespace App\Factory;

use App\Entity\MediasTypes;
use App\Repository\MediasTypesRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<MediasTypes>
 *
 * @method        MediasTypes|Proxy create(array|callable $attributes = [])
 * @method static MediasTypes|Proxy createOne(array $attributes = [])
 * @method static MediasTypes|Proxy find(object|array|mixed $criteria)
 * @method static MediasTypes|Proxy findOrCreate(array $attributes)
 * @method static MediasTypes|Proxy first(string $sortedField = 'id')
 * @method static MediasTypes|Proxy last(string $sortedField = 'id')
 * @method static MediasTypes|Proxy random(array $attributes = [])
 * @method static MediasTypes|Proxy randomOrCreate(array $attributes = [])
 * @method static MediasTypesRepository|RepositoryProxy repository()
 * @method static MediasTypes[]|Proxy[] all()
 * @method static MediasTypes[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static MediasTypes[]|Proxy[] createSequence(array|callable $sequence)
 * @method static MediasTypes[]|Proxy[] findBy(array $attributes)
 * @method static MediasTypes[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static MediasTypes[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class MediasTypesFactory extends ModelFactory
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
            'date_creation' => self::faker()->dateTime(),
            'date_modification' => self::faker()->dateTime(),
            'filetype' => self::faker()->text(255),
            'libellé' => 'image',
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(MediasTypes $mediasTypes): void {})
        ;
    }

    protected static function getClass(): string
    {
        return MediasTypes::class;
    }
}
