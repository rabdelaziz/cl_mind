<?php

namespace AppBundle\Service;


use AppBundle\Entity\Evaluation;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use AppBundle\Entity\Score;

class ManageCandidateEvaluation
{
    /**
     * [$entityManager description]
     * @var [type]
     */
    private $entityManager;
    /**
     * [$serializer description]
     * @var [type]
     */
    private $serializer;
    
    /**
     * [$session description]
     * @var [type]
     */
    private $session;
    
    /**
     * [__construct description]
     * @param ObjectManager $entityManager [description]
     * @param Serializer    $serializer    [description]
     */
	public function __construct(ObjectManager $entityManager, Serializer $serializer, SessionInterface $session)
	{
		$this->entityManager = $entityManager;
		$this->serializer = $serializer;
		$this->session = $session;
	}
    
    /**
     * [getSerializedQuestionsList description]
     * @param  [type] $questionsList [description]
     * @return [type]                [description]
     */
	public function getSerializedQuestionsList($questionsList)
	{
		$normalizer = $this->serializer->getNormalizers();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
         });

        $normalizer->setIgnoredAttributes(array('responses')); 

        $serializedQuestionsList = $this->serializer->serialize($questionsList, 'json');

        return $serializedQuestionsList;

	}

    /**
     * @param $evaluationId
     * @param $questionId
     * @param $qNumber
     * @param $user
     */
	public function saveScore($evaluationId, $questionId, $qNumber, $user)
    {
        $today = new \DateTime("now", new \DateTimeZone('Europe/paris'));
        $question =  $this->entityManager->getRepository('AppBundle:Question')->find($questionId);
        $evaluation =  $this->entityManager->getRepository('AppBundle:Evaluation')->find($evaluationId);
        $score = new Score();
        $score->setResponseDate($today);
        $score->setStartDate($today);
        $score->setQuestionNumber($qNumber);
        $score->setUser($user);
        $score->setQuestion($question);
        $score->setEvaluation($evaluation);
        $score->setTime(0);
        $score->setExpired(false);
        $score->setResponse(json_encode([]));
        $score->setScore(0);
        $this->entityManager->persist($score);
        $this->entityManager->flush();

    }

    /**
     * Enregistrement dans la table score le resultat pour chaque réponse
     * @param  string $evaluationId [description]
     * @param  array $responseIds  tableau des réponse du candidat
     * @param  string $questionId  
     * @param  intger $qNumber     l'ordre de la question            
     */
    public function updateScore($evaluationId, $responseIds, $questionId, $qNumber, $currentUser)
    {

        $now = new \DateTime("now");
        $evaluation =  $this->entityManager->getRepository('AppBundle:Evaluation')->find($evaluationId);
        $correctAnswer = array();
        $score = $this->entityManager->getRepository('AppBundle:Score')->findOneBy(array('evaluation' => $evaluationId, 'question' => $questionId, 'user' => $currentUser->getId()));

        
        $correctAnswers =  $this->entityManager->getRepository('AppBundle:Response')->findBy(array('question' => $questionId, 'correct' => 1));
        foreach ($correctAnswers as $ke => $choice) {
            $correctAnswer[] = $choice->getId();
        }
        $userScore = 0;
        if (!empty($responseIds)) {

            if (empty(array_diff($correctAnswer, $responseIds))) {
                 $userScore = 1;
            }
        }
        $time = $this->getDiffDateTime($score->getStartDate(), $now);

        $score->setResponseDate($now);
       // $score->setStartDate($this->session->get('currentQuestionStartDate')[$questionId]);
        $score->setQuestionNumber($qNumber);
       // $score->setUser($currentUser);
        //$score->setQuestion($question);
       // $score->setEvaluation($evaluation);
        $score->setExpired(true);
        $score->setTime($time);
        $score->setResponse(json_encode($responseIds));
        $score->setScore($userScore);
       // $this->entityManager->persist($score);
        $this->entityManager->flush();    
    }

    /**
     * @param $user
     * @param $evaluation
     * @return mixed
     */
    public function getFinalScoreByEvaluation($user, $evaluation)
    {
        $score = $this->entityManager->getRepository('AppBundle:Score')
            ->getUserFinalScore($user, $evaluation);

        if($score['time'] > 60 )
        {
            $score['second'] = $score['time'] % 60;
            $score['minute'] = floor($score['time'] / 60);
        } else {
            $score['second'] = $score['time'];
            $score['minute'] = 0;
        }

        return $score;
    }

    public function getDiffDateTime($startDate, $endDate)
    {
        return strtotime($endDate->format('Y-m-d H:i:s')) - strtotime($startDate->format('Y-m-d H:i:s'));
    }


}