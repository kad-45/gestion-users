<?php

namespace App\DataFixtures;

use App\Entity\Profil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfilFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $profil = new Profil();
        $profil->setRs(rs: 'Facebook');
        $profil->setUrl(url: 'https://www.facebook.com/louis.lave');

        $profil1 = new Profil();
        $profil1->setRs(rs: 'Twitter');
        $profil1->setUrl(url: 'https://www.twitter.com/louis.lave');

        $profil2 = new Profil();
        $profil2->setRs(rs: 'LinkedIn');
        $profil2->setUrl(url: 'https://www.linkedin.com/louis.lave');

        $profil3 = new Profil();
        $profil3->setRs(rs: 'instagrame');
        $profil3->setUrl(url: 'https://www.instagrame.com/louis.lave');

        $manager->persist($profil);
        $manager->persist($profil1);
        $manager->persist($profil2);
        $manager->persist($profil3);


        $manager->flush();
    }
}