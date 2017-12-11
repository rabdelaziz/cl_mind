<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

/*
 *
 */
class QuestionService
{

	/**
	 *
	 * @var EntityManager
	 */
	protected $em;

	public function __construct(EntityManager $entityManager)
	{
		$this->em = $entityManager;
	}


	/**
	 * Permet de vérifier si une question peut être supprimée
	 *
	 * @param \AppBundle\Entity\Question $question
	 * @return boolean
	 */
	public function canBeDelete(\AppBundle\Entity\Question $question)
	{
		return false;
	}

}