<?php

namespace App\Factory;

use App\Entity\Classe;
use App\Repository\ClasseRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Classe>
 *
 * @method        Classe|Proxy                              create(array|callable $attributes = [])
 * @method static Classe|Proxy                              createOne(array $attributes = [])
 * @method static Classe|Proxy                              find(object|array|mixed $criteria)
 * @method static Classe|Proxy                              findOrCreate(array $attributes)
 * @method static Classe|Proxy                              first(string $sortedField = 'id')
 * @method static Classe|Proxy                              last(string $sortedField = 'id')
 * @method static Classe|Proxy                              random(array $attributes = [])
 * @method static Classe|Proxy                              randomOrCreate(array $attributes = [])
 * @method static ClasseRepository|ProxyRepositoryDecorator repository()
 * @method static Classe[]|Proxy[]                          all()
 * @method static Classe[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Classe[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Classe[]|Proxy[]                          findBy(array $attributes)
 * @method static Classe[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Classe[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class ClasseFactory extends PersistentProxyObjectFactory
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
        return Classe::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Classe $classe): void {})
        ;
    }
}
