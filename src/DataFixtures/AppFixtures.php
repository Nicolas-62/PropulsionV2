<?php

namespace App\DataFixtures;


use App\Entity\Eleve;
use App\Factory\ClasseFactory;
use App\Factory\EleveFactory;
use App\Factory\MatiereFactory;
use App\Factory\NoteFactory;
use App\Factory\ProfesseurFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $matieres = [];
        // Ne fonctionne pas, truncater manuellement les tables
        foreach (range(1, 5) as $i) {
            $matieres[] = MatiereFactory::new()->setName($i)->create();
        }
        ProfesseurFactory::new()->setInfos()->createMany(
            12,
            function() { // note the callback - this ensures that each of the 5 comments has a different Post
                return ['matiere' => MatiereFactory::random()];
            }
        );

        ClasseFactory::createSequence(
            function() use ($matieres) {
                $alphabet = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');

                foreach (range(0, 10) as $i) {
                    yield [
                        'professeur' => ProfesseurFactory::random(),
                        'eleves' => EleveFactory::new()->afterPersist(function(Eleve $eleve, array $attributes) use ($matieres) {
                            foreach($matieres as $matiere) {
                                NoteFactory::new()->setInfos()->create(function() use ($eleve, $matiere) {
                                    return [
                                        'matiere' => $matiere,
                                        'eleve' => $eleve
                                    ];
                                });

                            }
                        })->setInfos()->many(20),
                        'nom' => 'Classe '.$alphabet[$i]
                    ];
                }
            }
        );

        $manager->flush();


    }
}
