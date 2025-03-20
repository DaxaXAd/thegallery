<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Liste des tags que tu souhaites précharger
        $tagNames = ['Fantaisie', 'ScienceFiction', 'Portrait', 'Abstrait', 'Médieval', 'Illustration', 'Dessin', 'ConceptArt', 'Aquarelle', 'Graphisme', 'Sketch', 'Horreur', 'Design', 'BD', 'Graphite', 'Calligraphie', 'Peinture', 'Photographie', 'PixelArt', 'FanArt', '3D', 'ComicBookArt', 'Manga'];

        foreach ($tagNames as $name) {
            $tag = new Tag();
            $tag->setNameTag($name);
            $manager->persist($tag);
        }

        // Envoie toutes les données dans la base
        $manager->flush();
    }
}
