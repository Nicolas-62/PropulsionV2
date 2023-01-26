<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Factory\ArticleFactory;
use App\Factory\CategoryFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use function Zenstruck\Foundry\factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        //$manager->flush();
        $factory = factory(Article::class);
        $factory->truncate();
        $factory = factory(Category::class);
        $factory->truncate();
        CategoryFactory::createMany(10, ['children' => CategoryFactory::new()->many(1,5)]);
        ArticleFactory::createMany(40, ['children' => ArticleFactory::new()->many(0,2)]);

    }
}
