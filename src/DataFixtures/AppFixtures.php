<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Config;
use App\Entity\Media;
use App\Entity\MediaLink;
use App\Entity\Mediaspec;
use App\Entity\Online;
use App\Entity\Projet;
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

        $config = new Config();
        $config->setTauxTVA(20);
        $manager->persist($config);

        $projet = new Projet();
        $projet->setRefInterne('PROJET-1');
        $projet->setRefExterne('123456');
        $projet->setClient('capside');
        $projet->setDateCreation(new \DateTime());
        $projet->setMontantHT(1000.00);
        $projet->setMontantTTC(1200);
        $manager->persist($projet);

        $projet = new Projet();
        $projet->setRefInterne('PROJET-2');
        $projet->setRefExterne('78891011');
        $projet->setDateCreation(new \DateTime());
        $projet->setClient('edf');
        $projet->setMontantHT(2540.50);
        $projet->setMontantTTC(3048.60);
        $manager->persist($projet);

        $manager->flush();
    }
}
