<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Evaluation;

class EvaluationFixture extends AbstractFixture implements OrderedFixtureInterface
{

	/**
	 * 
	 * {@inheritDoc}
	 * @see \Doctrine\Common\DataFixtures\FixtureInterface::load()
	 */
    public function load(ObjectManager $manager)
    {
        /*
        //evaluation 1
        $evaluation = new Evaluation();
        $evaluation->setAuthor($this->getReference('adminUser'));
        //$evaluation->setStartDate(new \DateTime());
        //$evaluation->setEndDate(new \DateTime()); echo "yesy1";
        $evaluation->addCandidate($this->getReference('testUser'));
        for ($i=0; $i < 5 ; $i++) { 
            $evaluation->addQuestion($this->getReference($i));
        }
        $manager->persist($evaluation);
        $manager->flush();

        // $this->addReference('evaluation1', $evaluation);
        //evaluation 2
        /* $evaluation2 = new Evaluation();
        $evaluation2->setAuthor($this->getReference('adminUser'));
        $evaluation2->setStartDate(new \DateTime());
        $evaluation2->setEndDate(new \DateTime());
        $manager->persist($evaluation2);
        $manager->flush();
        $this->addReference('evaluation2', $evaluation2); */

    }

    public function getOrder()
    {
        return 6;
    }
}