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
     * Enregistrement dans la table score le resultat pour chaque réponse
     * @param  string $evaluationId [description]
     * @param  array $responseIds  tableau des réponse du candidat
     * @param  string $questionId  
     * @param  intger $qNumber     l'ordre de la question            
     */
    public function saveScore($evaluationId, $responseIds, $questionId, $qNumber, $currentUser)
    {

        $today = new \DateTime("now", new \DateTimeZone('Europe/paris'));
     //   $user = $this->get('security.token_storage')->getToken()->getUser();
        //$em = $this->getDoctrine()->getManager();
        $evaluation =  $this->entityManager->getRepository('AppBundle:Evaluation')->find($evaluationId);
        $correctAnswer = array();
        $score = $this->entityManager->getRepository('AppBundle:Score')->findOneBy(array('evaluation' => $evaluationId, 'question' => $questionId, 'user' => $currentUser->getId()));

        if (empty($score)) {
            $score = new Score();
        } 

        $question =  $this->entityManager->getRepository('AppBundle:Question')->find($questionId);//var_dump($qId);die();
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
        $score->setResponseDate($today);
        $score->setStartDate($this->session->get('currentQuestionStartDate')[$questionId]);
        $score->setQuestionNumber($qNumber);
        $score->setUser($currentUser);
        $score->setQuestion($question);
        $score->setEvaluation($evaluation);


        $score->setResponse(json_encode($responseIds));
        $score->setScore($userScore);
        $this->entityManager->persist($score);
        $this->entityManager->flush();    
    }
}