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
        $tagNames = ['WebDesign', 'Nature'];

        foreach ($tagNames as $name) {
            $existing = $manager->getRepository(Tag::class)->findOneBy(['nameTag' => $name]);

            if (!$existing){
            $tag = new Tag();
            $tag->setNameTag($name);
            $manager->persist($tag);
            }
        }

        // Envoie toutes les données dans la base
        $manager->flush();
    }
}
