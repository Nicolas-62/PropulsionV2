<?php

namespace App\Factory;

use App\Entity\Langues;
use App\Repository\LanguesRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Langues>
 *
 * @method        Langues|Proxy create(array|callable $attributes = [])
 * @method static Langues|Proxy createOne(array $attributes = [])
 * @method static Langues|Proxy find(object|array|mixed $criteria)
 * @method static Langues|Proxy findOrCreate(array $attributes)
 * @method static Langues|Proxy first(string $sortedField = 'id')
 * @method static Langues|Proxy last(string $sortedField = 'id')
 * @method static Langues|Proxy random(array $attributes = [])
 * @method static Langues|Proxy randomOrCreate(array $attributes = [])
 * @method static LanguesRepository|RepositoryProxy repository()
 * @method static Langues[]|Proxy[] all()
 * @method static Langues[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Langues[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Langues[]|Proxy[] findBy(array $attributes)
 * @method static Langues[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Langues[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class LanguesFactory extends ModelFactory
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
            'code' => 'fr',
            'date_creation' => self::faker()->dateTime(),
            'date_modification' => self::faker()->dateTime(),
            'label' => 'Français',
            'ordre' => 1,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Langues $langues): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Langues::class;
    }
}
