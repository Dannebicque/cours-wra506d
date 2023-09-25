<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Génère 40 films avec un titre, une date de sortie, une durée, un synopsis, une catégorie (en lien avec les autres fixtures) et entre 2 et 4 acteurs, diffé (en lien avec les autres fixtures)

        foreach (range(1, 40) as $i) {
            $movie = new Movie();
            $movie->setTitle('Movie ' . $i);
            $movie->setReleaseDate(new \DateTime());
            $movie->setDuration(rand(60, 180));
            $movie->setDescription('Synopsis ' . $i);
            $movie->setCategory($this->getReference('category_' . rand(1, 5)));
//            $movie->addActor($this->getReference('actor_' . rand(1, 10)));
//            $movie->addActor($this->getReference('actor_' . rand(1, 10)));
//            $movie->addActor($this->getReference('actor_' . rand(1, 10)));
//            $movie->addActor($this->getReference('actor_' . rand(1, 10)));

            // Ajoute entre 2 et 6 acteurs dans le films, tous différents en se basant sur les fixtures
            $actors = [];
            foreach (range(1, rand(2, 6)) as $j) {
                $actor = $this->getReference('actor_' . rand(1, 10));
                if (!in_array($actor, $actors)) {
                    $actors[] = $actor;
                    $movie->addActor($actor);
                }
            }

            $manager->persist($movie);
        }

        $manager->flush();
    }
}
