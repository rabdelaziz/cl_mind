<?php

namespace AppBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Evaluation;
use AppBundle\Entity\Topic;
use Doctrine\ORM\EntityManager;
use AppBundle\Exception\QuestionException;

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

    /**
     * Permet de calculer le nombre de questions à générer par difficulté
     * 
     * @param \AppBundle\Entity\Difficulty $difficulty
     * @param int $nbQuestion
     * @return int[]
     */
    public function calculNbQuestion(\AppBundle\Entity\Difficulty $difficulty, $nbQuestion)
    {
        $nbFacile = (int) round($nbQuestion * $difficulty->getPercentageEasy() / 100);
        $nbMoyen = (int) round($nbQuestion * $difficulty->getPercentageAverage() / 100);
        $nbDifficile = (int) round($nbQuestion * $difficulty->getPercentageDifficult() / 100);

        // Calculer la différence pour éventuellement réajuster le nombre
        $diff = $nbQuestion - ($nbFacile + $nbMoyen + $nbDifficile);

        // Le nombre en plus ou manquant sera ajouté ou déduit des questions moyennes
        $nbMoyen += $diff;
        $values = array(
            'facile' => $nbFacile,
            'moyen' => $nbMoyen,
            'difficile' => $nbDifficile
        );

        return $values;
    }

    /**
     * Permet de générer les questions pour une évaluation
     * 
     * @param Evaluation $evaluation
     * @param ArrayCollection $topics
     * @param array $levels
     * @param array $expectedNbQuestions
     * @return \AppBundle\Entity\Evaluation
     */
    public function generateQuestions(Evaluation $evaluation, ArrayCollection $topics, Array $levels, Array $expectedNbQuestions)
    {
        // Génerer les questions
        $allQuestions = array();
        foreach ($topics as $topic) {

            if ($topic instanceof Topic && isset($expectedNbQuestions[$topic->getId()])) {

                $nbQuestionAttendu = $expectedNbQuestions[$topic->getId()];
                // Calculer le nb de questions par niveau
                $nbQuestionValues = $this->calculNbQuestion($evaluation->getDifficulty(), $nbQuestionAttendu);
                foreach ($levels as $level) {
                    $levelName = strtolower($level->getName());
                    $levelNbQuestion = isset($nbQuestionValues[$levelName]) ? (int) $nbQuestionValues[$levelName] : 0;

                    // Récupérer  ($levelNbQuestion) questions selon le thème, le niveau 
                    if (!empty($nbQuestionValues[$levelName])) {
                        $listQuestion = $this->em->getRepository('AppBundle:Question')
                                ->getQuestionsByTopicIdAndLevelId($topic->getId(), $level->getId(), $levelNbQuestion);

                        if (count($listQuestion) < $nbQuestionValues[$levelName]) {
                            throw new QuestionException($topic->getName() . '[' . $level->getName() . '] : Nombre de questions disponibles: ' . count($listQuestion) . '/' . $nbQuestionValues[$levelName]);
                        }

                        $allQuestions = array_merge($allQuestions, $listQuestion);
                    }
                }
            }
        }

        foreach ($allQuestions as $question) {
            $evaluation->addQuestion($question);
        }

        return $evaluation;
    }
}
