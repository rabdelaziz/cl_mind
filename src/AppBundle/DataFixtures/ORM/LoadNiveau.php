<?php

namespace AppBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Niveau;

class LoadNiveau implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $listeNiveau = array(
            'moyen',
            'difficile',
        );

        foreach($listeNiveau as $value) {
            $niveau = new Niveau();

            $niveau->setNom($value);

            // On la persiste
            $manager->persist($niveau);
        }
        $manager->flush();
    }
}