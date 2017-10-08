<?php

namespace AppBundle\Service;


use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Difficulty;
use AppBundle\Entity\Evaluation;
use AppBundle\Entity\Topic;
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
		return true;
	}
}