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
use App\Factory\LanguageFactory;
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
        // Ne fonctionne pas, truncater manuellement les tables

//        $factory = factory(Article::class);
//        $factory->truncate();
//        $factory = factory(Category::class);
//        $factory->truncate();
//        $factory = factory(MediaLink::class);
//        $factory->truncate();
//        $factory = factory(Online::class);
//        $factory->truncate();
//        $factory = factory(Mediaspec::class);
//        $factory->truncate();
//        $factory = factory(Media::class);
//        $factory->truncate();

        MediasTypesFactory::createOne();
        LanguageFactory::createOne();

//        CategoryFactory::createMany(4,
//            function(){
//                return [
//                    'articles' => ArticleFactory::createMany(3, ['children' => ArticleFactory::new()->many(0,2)]),
//                    'parent'   => CategoryFactory::createOne(),
//                ];
//            }
//        );

//        OnlineFactory::createMany(5);
        //MediaFactory::createMany(60);
        //MediaLinkFactory::createMany(40);
        //MediaspecFactory::createMany(40);
    }
}
