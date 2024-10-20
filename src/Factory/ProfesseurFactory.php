<?php

namespace App\Factory;

use App\Entity\Professeur;
use App\Repository\ProfesseurRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Professeur>
 *
 * @method        Professeur|Proxy                              create(array|callable $attributes = [])
 * @method static Professeur|Proxy                              createOne(array $attributes = [])
 * @method static Professeur|Proxy                              find(object|array|mixed $criteria)
 * @method static Professeur|Proxy                              findOrCreate(array $attributes)
 * @method static Professeur|Proxy                              first(string $sortedField = 'id')
 * @method static Professeur|Proxy                              last(string $sortedField = 'id')
 * @method static Professeur|Proxy                              random(array $attributes = [])
 * @method static Professeur|Proxy                              randomOrCreate(array $attributes = [])
 * @method static ProfesseurRepository|ProxyRepositoryDecorator repository()
 * @method static Professeur[]|Proxy[]                          all()
 * @method static Professeur[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Professeur[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Professeur[]|Proxy[]                          findBy(array $attributes)
 * @method static Professeur[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Professeur[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class ProfesseurFactory extends PersistentProxyObjectFactory
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
        return Professeur::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'nom' => self::faker()->lastName("Female" | "Male"),
            'prenom' => self::faker()->firstName("Female" | "Male"),
            'sexe' => self::faker()->randomElement(["Homme", "Femme"]),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Professeur $professeur): void {})
        ;
    }

    public function setInfos(): self
    {
        $genres         = ['Homme', 'Femme', 'Non binaire'];
        $factory_genres = ['male','female'];

        return $this->with(function () use ($genres, $factory_genres) {
            $choice_index = rand(0, count($genres) - 1);
            $genre_label = $genres[$choice_index];
            if($choice_index > 1){
                $factory_gender_index = rand(0,1);
            }else{
                $factory_gender_index = $choice_index;
            }
            return [
                'nom' => self::faker()->lastName($factory_genres[$factory_gender_index]),
                'prenom' => self::faker()->firstName($factory_genres[$factory_gender_index]),
                'sexe' => self::faker()->randomElement([$genre_label])
            ];
        });
    }
}
