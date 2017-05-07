<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Niveau;

class NiveauFixture extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * load level
     * @param  ObjectManager $manager
     * @return Niveau                
     */
    public function load(ObjectManager $manager)
    {
        $levelList = array(
            'facile',
            'moyen',
            'difficile',
        );

        foreach($levelList as $value) {
            $niveau = new Niveau();
            $niveau->setNom($value);
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