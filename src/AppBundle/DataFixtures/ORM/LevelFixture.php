<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Level;

class LevelFixture extends AbstractFixture implements OrderedFixtureInterface
{

	/**
	 * 
	 * {@inheritDoc}
	 * @see \Doctrine\Common\DataFixtures\FixtureInterface::load()
	 */
    public function load(ObjectManager $manager)
    {
        $levelList = array(
            'facile',
            'moyen',
            'difficile',
        );

        foreach($levelList as $value) {
            $niveau = new Level();
            $niveau->setName($value);
            $manager->persist($niveau);
            $manager->flush();
            $this->addReference($value, $niveau);
            // On la persiste
          
        }
       
    }

    public function getOrder()
    {
        return 2;
    }
}