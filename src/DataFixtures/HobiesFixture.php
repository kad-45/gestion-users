<?php

namespace App\DataFixtures;

use App\Entity\Hobies;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HobiesFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        
        $data = [
            "Yoga",
            "Cuisine",
            "pâtisserie",
            "Photographie",
            "Blogging",
            "Lecture",
            "Apprendre une langue",
            "Construction Lego",
            "Dessin",
            "coloriage",
            "peinture",
            "Se lancer dans le tissage de tapis",
            "Créer des vêtements ou des cosplay",
            "Fabriquer des bijoux",
            "Travailler le métal",
            "Décorer des galets",
            "Faire des puzzles avec de plus en plus de pièces",
            "Créer des miniatures (maisons, voitures, trains, bateaux...)",
            "Améliorer son espace de vie",
            "Apprendre à jongler",
            "Faire partie d’un club de lecture",
            "Apprendre la programmation informatique",
            "En apprendre plus sur le survivalisme",
            "Créer une chaine Youtube",
            "Jouer au fléchettes",
            "Apprendre à chanter",
        ];
        
        for ($i =0; $i < count($data); $i++) 
        {
            $hobies = new Hobies();
            $hobies->setDesignation($data[$i]);
            $manager->persist($hobies);
     
        }

        $manager->flush();
    }
}