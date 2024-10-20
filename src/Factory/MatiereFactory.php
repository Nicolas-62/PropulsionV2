<?php

namespace App\Factory;

use App\Entity\Matiere;
use App\Repository\MatiereRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Matiere>
 *
 * @method        Matiere|Proxy                              create(array|callable $attributes = [])
 * @method static Matiere|Proxy                              createOne(array $attributes = [])
 * @method static Matiere|Proxy                              find(object|array|mixed $criteria)
 * @method static Matiere|Proxy                              findOrCreate(array $attributes)
 * @method static Matiere|Proxy                              first(string $sortedField = 'id')
 * @method static Matiere|Proxy                              last(string $sortedField = 'id')
 * @method static Matiere|Proxy                              random(array $attributes = [])
 * @method static Matiere|Proxy                              randomOrCreate(array $attributes = [])
 * @method static MatiereRepository|ProxyRepositoryDecorator repository()
 * @method static Matiere[]|Proxy[]                          all()
 * @method static Matiere[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Matiere[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Matiere[]|Proxy[]                          findBy(array $attributes)
 * @method static Matiere[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Matiere[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class MatiereFactory extends PersistentProxyObjectFactory
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
        return Matiere::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {

        return [
            'nom' => self::faker()->text(255),
            'professeur' => ProfesseurFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Matiere $matiere): void {})
        ;
    }

    public function setName(string $index): self
    {
        $matieres = [
            'Mathmatiques',
            'Sport',
            'Français',
            'Histoire/Géographie',
            'Biologie',
            'Physique/Chimie'
        ];
        return $this->with(function () use ($index, $matieres) {
            return ['nom' => $matieres[$index]];
        });
    }
}
