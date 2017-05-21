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
class EvaluationService
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
	 * Permet de récupérer la liste de noms de tous les thèmes de l'évaluation
	 * 
	 * @param \AppBundle\Entity\Evaluation $evaluation
	 * @return array|[]
	 */
	public function getDistinctTopicsName(\AppBundle\Entity\Evaluation $evaluation)
	{
		$topicList = array();
		foreach ($evaluation->getQuestions() as $question) {
			if (!isset($topicList[$question->getTopic()->getId()])) {
				$topicList[$question->getTopic()->getId()] = $question->getTopic()->getName();
			}
		}
		
		return $topicList;
	}
	
	/**
	 * Permet de vérifier si une évaluation peut être supprimée
	 * 
	 * @param \AppBundle\Entity\Evaluation $evaluation
	 * @return boolean
	 */
	public function canBeDelete(\AppBundle\Entity\Evaluation $evaluation)
	{
		return $evaluation->getCandidates()->count() == 0;
	}
	
	
	public function calculNbQuestion(\AppBundle\Entity\Difficulty $difficulty, $nbQuestion)
	{
		$nbFacile = $nbQuestion * $difficulty->getPercentageEasy() / 100;
		$nbMoyen = $nbQuestion * $difficulty->getPercentageAverage() / 100;
		$nbDifficile = $nbQuestion * $difficulty->getPercentageDifficult() / 100;

		$nbManquant = ceil( ($nbFacile - (int)$nbFacile) + ($nbMoyen - (int)$nbMoyen) + ($nbDifficile - (int)$nbDifficile));
	
		$values = array(
				'facile' => (int)$nbFacile,
				'moyen' => (int)$nbMoyen + (int)$nbManquant,
				'difficile' => (int)$nbDifficile
		);

		return $values;
	}
	
	public function generateQuestions(Evaluation $evaluation, ArrayCollection $topics, Array $levels, Array $expectedNbQuestions, Difficulty $difficulty)
	{ 
		// Génerer les questions
		$allQuestions = array();
		$repository = $this->em->getRepository('AppBundle:Question');
		foreach ($topics as $topic) {
			 
			if ($topic instanceof Topic && isset($expectedNbQuestions[$topic->getId()])) {
		
				$nbQuestionAttendu = $expectedNbQuestions[$topic->getId()];
				// Calculer le nb de question par niveau
				$nbQuestionValues = $this->calculNbQuestion($difficulty, $nbQuestionAttendu);				
				foreach ($levels as $level) {
					$levelNbQuestion = isset($nbQuestionValues[$level->getName()]) ? (int)$nbQuestionValues[$level->getName()] : 0;
					$listQuestion = $repository->getQuestionsWithTopic($topic->getName(), $level->getName(), $levelNbQuestion);
					
					$allQuestions = array_merge($allQuestions, $listQuestion);
				}
			}
		}
		
		foreach ($allQuestions as $question) {
			$evaluation->addQuestion($question);
		}

		return $evaluation;
	}
}