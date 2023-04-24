<?php

namespace App\Factory;

use App\Constants\Constants;
use App\Entity\Language;
use App\Repository\LanguageRepository;
use DateTimeImmutable;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Language>
 *
 * @method        Language|Proxy create(array|callable $attributes = [])
 * @method static Language|Proxy createOne(array $attributes = [])
 * @method static Language|Proxy find(object|array|mixed $criteria)
 * @method static Language|Proxy findOrCreate(array $attributes)
 * @method static Language|Proxy first(string $sortedField = 'id')
 * @method static Language|Proxy last(string $sortedField = 'id')
 * @method static Language|Proxy random(array $attributes = [])
 * @method static Language|Proxy randomOrCreate(array $attributes = [])
 * @method static LanguageRepository|RepositoryProxy repository()
 * @method static Language[]|Proxy[] all()
 * @method static Language[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Language[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Language[]|Proxy[] findBy(array $attributes)
 * @method static Language[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Language[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class LanguageFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(
        // Code langue
        private string $locale
    )
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
            'code' => $this->locale,
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
            // ->afterInstantiate(function(Language $Language): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Language::class;
    }
}
