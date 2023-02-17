<?php

namespace App\Factory;

use App\Entity\Online;
use App\Repository\OnlinesRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Online>
 *
 * @method        Online|Proxy create(array|callable $attributes = [])
 * @method static Online|Proxy createOne(array $attributes = [])
 * @method static Online|Proxy find(object|array|mixed $criteria)
 * @method static Online|Proxy findOrCreate(array $attributes)
 * @method static Online|Proxy first(string $sortedField = 'id')
 * @method static Online|Proxy last(string $sortedField = 'id')
 * @method static Online|Proxy random(array $attributes = [])
 * @method static Online|Proxy randomOrCreate(array $attributes = [])
 * @method static OnlinesRepository|RepositoryProxy repository()
 * @method static Online[]|Proxy[] all()
 * @method static Online[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Online[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Online[]|Proxy[] findBy(array $attributes)
 * @method static Online[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Online[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class OnlineFactory extends ModelFactory
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
            'langue' => LanguesFactory::random(),
            'online' => 1,
            'article' => null,
            'category' => CategoryFactory::random(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Online $online): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Online::class;
    }
}
