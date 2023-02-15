<?php

namespace App\Factory;

use App\Entity\Media;
use App\Entity\MediasTypes;
use App\Repository\MediaRepository;
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

        $mediaType = $this->entityManager->getRepository(MediasTypes::class)->find(1);
        return [
            'date_creation' => self::faker()->dateTime(),
            'date_modification' => self::faker()->dateTime(),
            'fichier' => self::faker()->imageUrl(640, 480, 'animals'),
            //'fichier' => self::faker()->text(255),
            'media_type_id' => MediasTypesFactory::random(),
            'article' => ArticleFactory::randomOrCreate(),
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
