<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Difficulty;

class DifficultyFixture extends AbstractFixture implements OrderedFixtureInterface
{

	/**
	 * 
	 * {@inheritDoc}
	 * @see \Doctrine\Common\DataFixtures\FixtureInterface::load()
	 */
	public function load(ObjectManager $manager)
	{		
		$difficulties = [
			[
				'name' => 'facile',
				'description' => "Moins d'un an d'expérience",
				'coef_facile' => 35,
				'coef_moyen' => 35,
				'coef_difficile' => 30,
			],
			[
				'name' => 'moyen',
				'description' => "Entre 1 an et 2 ans d'expérience",
				'coef_facile' => 25,
				'coef_moyen' => 50,
				'coef_difficile' => 25,
			],
			[
				'name' => 'difficile',
				'description' => "Entre 2 ans et 5 ans d'expérience",
				'coef_facile' => 15,
				'coef_moyen' => 35,
				'coef_difficile' => 50,
			],
			[
			'name' => 'très difficile',
			'description' => "Plus de 5 ans d'expérience",
			'coef_facile' => 10,
			'coef_moyen' => 30,
			'coef_difficile' => 60,
			],
		];

		foreach($difficulties as $value) {
			$difficulty = new Difficulty();
			$difficulty
				->setName($value['name'])
				->setDescription($value['description'])
				->setPercentageEasy($value['coef_facile'])
				->setPercentageAverage($value['coef_moyen'])
				->setPercentageDifficult($value['coef_difficile']);
			
			$manager->persist($difficulty);
			$manager->flush();
		}
		 
	}

	public function getOrder()
	{
		return 2;
	}
}