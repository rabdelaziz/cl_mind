<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Status;

class StatusFixture extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * load level
	 * @param  ObjectManager $manager
	 * @return Status
	 */
	public function load(ObjectManager $manager)
	{
		$statusValues = array(
			'actif',
			'inactif',
			'archiver',
		);

		foreach($statusValues as $value) {
			$status = new Status();
			$status->setName($value);
			$manager->persist($status);
			$manager->flush();
			$this->addReference($value, $status);
			// On la persiste
		}		 
	}

	public function getOrder()
	{
		return 2;
	}
}