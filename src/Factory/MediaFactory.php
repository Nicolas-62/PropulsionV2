<?php

namespace App\Factory;

use App\Entity\Media;
use App\Entity\MediaType;
use App\Repository\MediaRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Media>
 *
 * @method        Media|Proxy create(array|callable $attributes = [])
 * @method static Media|Proxy createOne(array $attributes = [])
 * @method static Media|Proxy find(object|array|mixed $criteria)
 * @method static Media|Proxy findOrCreate(array $attributes)
 * @method static Media|Proxy first(string $sortedField = 'id')
 * @method static Media|Proxy last(string $sortedField = 'id')
 * @method static Media|Proxy random(array $attributes = [])
 * @method static Media|Proxy randomOrCreate(array $attributes = [])
 * @method static MediaRepository|RepositoryProxy repository()
 * @method static Media[]|Proxy[] all()
 * @method static Media[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Media[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Media[]|Proxy[] findBy(array $attributes)
 * @method static Media[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Media[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class MediaFactory extends ModelFactory
{


    private EntityManagerInterface $entityManager;

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager  = $entityManager;
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
            'file' => self::faker()->imageUrl(640, 480, 'animals'),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Media $media): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Media::class;
    }
}
