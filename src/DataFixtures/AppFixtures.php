<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Media;
use App\Factory\ArticleFactory;
use App\Factory\CategoryFactory;
use App\Factory\LanguesFactory;
use App\Factory\MediaFactory;
use App\Factory\MediasTypesFactory;
use App\Factory\OnlineFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use function Zenstruck\Foundry\factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $factory = factory(Media::class);
        $factory->truncate();
        $factory = factory(Article::class);
        $factory->truncate();
        $factory = factory(Category::class);
        $factory->truncate();

        LanguesFactory::createOne();
        CategoryFactory::createMany(10, ['children' => CategoryFactory::new()->many(1,5)]);
        ArticleFactory::createMany(40, ['children' => ArticleFactory::new()->many(0,2)]);
        OnlineFactory::createMany(40);
        MediasTypesFactory::createOne();
        MediaFactory::createMany(60);

    }
}
