<?php

namespace App\Factory;

use App\Entity\Section;
use App\Repository\SectionRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Section>
 *
 * @method        Section|Proxy create(array|callable $attributes = [])
 * @method static Section|Proxy createOne(array $attributes = [])
 * @method static Section|Proxy find(object|array|mixed $criteria)
 * @method static Section|Proxy findOrCreate(array $attributes)
 * @method static Section|Proxy first(string $sortedField = 'id')
 * @method static Section|Proxy last(string $sortedField = 'id')
 * @method static Section|Proxy random(array $attributes = [])
 * @method static Section|Proxy randomOrCreate(array $attributes = [])
 * @method static SectionRepository|RepositoryProxy repository()
 * @method static Section[]|Proxy[] all()
 * @method static Section[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Section[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Section[]|Proxy[] findBy(array $attributes)
 * @method static Section[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Section[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class SectionFactory extends ModelFactory
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
            'title' => self::faker()->sentence(1, false),
            'position' => self::faker()->numberBetween(1,100),
            'canCreate' => self::faker()->boolean(),
            'hasContent' => self::faker()->boolean(),
            'hasLink' => self::faker()->boolean(),
            'hasMulti' => self::faker()->boolean(),
            'hasSeo' => self::faker()->boolean(),
            'hasSubTitle' => self::faker()->boolean(),
            'hasTheme' => self::faker()->boolean(),
            'hasTitle' => self::faker()->boolean(),
            'created_at' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'updated_at' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'category' => CategoryFactory::randomOrCreate()
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Section $section): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Section::class;
    }
}
