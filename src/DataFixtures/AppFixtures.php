<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Media;
use App\Entity\MediaLink;
use App\Entity\Mediaspec;
use App\Entity\Online;
use App\Factory\ArticleFactory;
use App\Factory\CategoryFactory;
use App\Factory\LanguesFactory;
use App\Factory\MediaFactory;
use App\Factory\MediaLinkFactory;
use App\Factory\MediaspecFactory;
use App\Factory\MediasTypesFactory;
use App\Factory\OnlineFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use function Zenstruck\Foundry\factory;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $factory = factory(Article::class);
        $factory->truncate();
        $factory = factory(Category::class);
        $factory->truncate();
        $factory = factory(MediaLink::class);
        $factory->truncate();
        $factory = factory(Online::class);
        $factory->truncate();
        $factory = factory(Mediaspec::class);
        $factory->truncate();
        $factory = factory(Media::class);
        $factory->truncate();

        MediasTypesFactory::createOne();
        LanguesFactory::createOne();

        CategoryFactory::createMany(10, ['children' => CategoryFactory::new()->many(1,5)]);
        ArticleFactory::createMany(40, ['children' => ArticleFactory::new()->many(0,2)]);
        OnlineFactory::createMany(40);
        MediaFactory::createMany(60);
        MediaLinkFactory::createMany(40);
        MediaspecFactory::createMany(40);
    }
}
