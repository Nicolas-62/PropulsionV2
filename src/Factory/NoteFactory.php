<?php

namespace App\Factory;

use App\Entity\Note;
use App\Repository\NoteRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Note>
 *
 * @method        Note|Proxy                              create(array|callable $attributes = [])
 * @method static Note|Proxy                              createOne(array $attributes = [])
 * @method static Note|Proxy                              find(object|array|mixed $criteria)
 * @method static Note|Proxy                              findOrCreate(array $attributes)
 * @method static Note|Proxy                              first(string $sortedField = 'id')
 * @method static Note|Proxy                              last(string $sortedField = 'id')
 * @method static Note|Proxy                              random(array $attributes = [])
 * @method static Note|Proxy                              randomOrCreate(array $attributes = [])
 * @method static NoteRepository|ProxyRepositoryDecorator repository()
 * @method static Note[]|Proxy[]                          all()
 * @method static Note[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Note[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Note[]|Proxy[]                          findBy(array $attributes)
 * @method static Note[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Note[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class NoteFactory extends PersistentProxyObjectFactory
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
        return Note::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {

        return [
            'eleve' => EleveFactory::new(),
            'matiere' => MatiereFactory::new(),
            'valeur' => self::faker()->randomNumber(2),
            'rate' => self::faker()->realTextBetween(150, 400)
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Note $note): void {})
        ;
    }

    public function setInfos(): self
    {
        $note         = rand(3, 20);
        return $this->with(function () use ($note) {
            return [
                'valeur' => $note
            ];
        });
    }
}
