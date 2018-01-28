<?php

namespace AppBundle\Service;



use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Snappy\Pdf;

use AppBundle\Entity\Score;
use AppBundle\Entity\User;
use AppBundle\Entity\Evaluation;
use function var_dump;

class ManageCandidateEvaluation
{
    const SECRET_KEY = 'check_dev_cl_mind17';
    const SECRET_IV = 'check_dev_cl_mind17';

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
	public function __construct(ObjectManager $entityManager, Serializer $serializer, SessionInterface $session, $mailer, Pdf $pdf)
	{
		$this->entityManager = $entityManager;
		$this->serializer = $serializer;
		$this->session = $session;
        $this->mailer = $mailer;
        $this->pdf = $pdf;
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
    public function getFinalScoreByEvaluation(User $user, Evaluation $evaluation)
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

    public function getFinalDetailScore(User $user, Evaluation $evaluation)
    {
         return $this->entityManager->getRepository('AppBundle:Score')
            ->getUserDetailFinalScore($user, $evaluation);

    }

    public function getDiffDateTime($startDate, $endDate)
    {
        return strtotime($endDate->format('Y-m-d H:i:s')) - strtotime($startDate->format('Y-m-d H:i:s'));
    }

    public function sendResultReport(User $user, Evaluation $evaluation, $path)
    {

        $detailResult = $this->getFinalDetailScore($user, $evaluation);
        $path = $this->generateRreportPdf($user, $evaluation, $detailResult, $path);
        $this->mailer->sendResultReport($user, $evaluation, $path);

    }

    public function generateRreportPdf($user, $evaluation,$detailResult, $path)
    {
        $filename = $evaluation->getId() .'_' .  $user->getId();
        $filename = sprintf($evaluation->getId() . $filename. "-%s.pdf", date('Y-m-d m:s'));
        $this->pdf->generateFromHtml(
            $this->mailer->templating->render(
                'Emails/reportResult.html.twig',
                array(
                    'detailResult'  => $detailResult,
                    'candidat' => $user,
                    'evaluation' => $evaluation
                )
            ),
            $path . '/' . $filename
        );

        return $path . '/' . $filename;
    }

    /**
     * Encode/Decode de email pour la connextion
     * @param $string
     * @param string $action
     * @return bool|string
     */
    public function makeSSODataConnection($string, $action = 'e')
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', self::SECRET_KEY );
        $iv = substr( hash( 'sha256', self::SECRET_IV ), 0, 16 );

        if( $action === 'e' ) {
            $output = base64_encode(openssl_encrypt( $string, $encrypt_method, $key, 0, $iv));
        } else if( $action === 'd' ){
            $output = openssl_decrypt(base64_decode( $string ), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    /**
     * Envoi de lien de connexion au candidat
     * @param User $candidate
     */
    public function sendLinkEvaluation(User $candidate)
    {
        $subject = "Votre Evaluation [Clevermind]";
        $userName = $this->makeSSODataConnection($candidate->getEmail(), $action = 'e');

        $body = $this->mailer->templating->render(
            'Emails/testAvailable.html.twig',
            array(
                'candidate'  => $candidate,
                'userName' => $userName,
            )
        );
        $this->mailer->sendMessage('ram.abdelaziz@gmail.com', $subject, $body, $path = null);
    }

}