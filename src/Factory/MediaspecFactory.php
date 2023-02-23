<?php

namespace App\Factory;

use App\Entity\Mediaspec;
use App\Repository\MediaspecsRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Mediaspec>
 *
 * @method        Mediaspec|Proxy create(array|callable $attributes = [])
 * @method static Mediaspec|Proxy createOne(array $attributes = [])
 * @method static Mediaspec|Proxy find(object|array|mixed $criteria)
 * @method static Mediaspec|Proxy findOrCreate(array $attributes)
 * @method static Mediaspec|Proxy first(string $sortedField = 'id')
 * @method static Mediaspec|Proxy last(string $sortedField = 'id')
 * @method static Mediaspec|Proxy random(array $attributes = [])
 * @method static Mediaspec|Proxy randomOrCreate(array $attributes = [])
 * @method static MediaspecsRepository|RepositoryProxy repository()
 * @method static Mediaspec[]|Proxy[] all()
 * @method static Mediaspec[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Mediaspec[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Mediaspec[]|Proxy[] findBy(array $attributes)
 * @method static Mediaspec[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Mediaspec[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class MediaspecFactory extends ModelFactory
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
            'haslegend' => self::faker()->boolean(),
            'heritage' => 0,
            'mandatory' => 1,
            'name' => self::faker()->text(10),
            'width' => self::faker()->numberBetween(300,800),
            'height' => self::faker()->numberBetween(300,800),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Mediaspec $mediaspec): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Mediaspec::class;
    }
}
