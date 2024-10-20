<?php

namespace App\Factory;

use App\Entity\Config;
use App\Repository\ConfigRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Config>
 *
 * @method        Config|Proxy                              create(array|callable $attributes = [])
 * @method static Config|Proxy                              createOne(array $attributes = [])
 * @method static Config|Proxy                              find(object|array|mixed $criteria)
 * @method static Config|Proxy                              findOrCreate(array $attributes)
 * @method static Config|Proxy                              first(string $sortedField = 'id')
 * @method static Config|Proxy                              last(string $sortedField = 'id')
 * @method static Config|Proxy                              random(array $attributes = [])
 * @method static Config|Proxy                              randomOrCreate(array $attributes = [])
 * @method static ConfigRepository|ProxyRepositoryDecorator repository()
 * @method static Config[]|Proxy[]                          all()
 * @method static Config[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Config[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Config[]|Proxy[]                          findBy(array $attributes)
 * @method static Config[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Config[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class ConfigFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Config::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'created_at' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'updated_at' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Config $config): void {})
        ;
    }
}
