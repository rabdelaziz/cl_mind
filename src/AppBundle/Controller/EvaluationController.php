<?php
/**
 * Created by PhpStorm.
 * User: ramzi
 * Date: 26/05/2016
 * Time: 22:52
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Repository;
use AppBundle\Entity\Score;


use AppBundle\Form\TopicType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class EvaluationController extends Controller
{
    /**
     * @Route("evaluation/new_topic", name="create_topic", options={"expose"=true})
     */
    public function createTopicAction(Request $request)
    {
        $form = $this->createForm(TopicType::class);
        $form->handleRequest($request);
        //var_dump($form->isSubmitted() , $form->isValid());die;
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \AppBundle\Entity\Topic $topic */
            $topic = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($topic);
            $em->flush();
            $response = new JsonResponse();
            $response->headers->set('Content-Type', 'application/json');
            $response->setData(['status' => 'ok', 'msg' => utf8_encode("Vos modification ont été bien enregistrés"), 'topic' => $topic], 200);
            return $response;
            //  return new JsonResponse(['status' => 'ok', 'msg' => "Vos modification ont été bien enregistrés", 'topic' => $topic],  200);
//            return $this->redirectToRoute('topic_list');
        }
        return $this->render('forms/topic/create_topic.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("evaluation/topic_list", name="topic_list")
     */
    public function topicListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sessionList = $em->getRepository('AppBundle:Topic')->findAll();


        return $this->render('topic/topic_liste.html.twig', array('sessionList' => $sessionList,
            'activateItem' => 1,
        ));

    }


    /**
     * @Route("evaluation/start", name="start_evaluation")
     */
    public function startTestAction()
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();
        $questionNumber  = 0;
        $currentSession = $em->getRepository('AppBundle:Evaluation')->getCurrentEvaluation($currentUser->getId());
        $questionsList = $currentSession->getQuestions();
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer(array($normalizer), array(new JsonEncoder()));
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });

        $normalizer->setIgnoredAttributes(array('responses'));

        $serializedQuestionsList = $serializer->serialize($questionsList, 'json');
        $score = $em->getRepository('AppBundle:Score')->findOneBy(  array('evaluation' => $currentSession->getId(),
                                                                        'user' => $currentUser->getId()),
                                                                    array('id' => 'DESC')
                                                                ); 

        if(!empty($score)) {
            $questionNumber = $score->getQuestionNumber();
            $questionNumber++;
        }

        return $this->render('Evaluation/evaluation.html.twig', ['sId' => $currentSession->getId(),
            'firstQuestion' => $questionsList[$questionNumber],
            'questionNumber' => $questionNumber,
            'sessionQuestion' => json_decode($serializedQuestionsList)]);
    }

    /**
     * @Route("user/evaluation/started", name="checkQuestionResponse")
     * [checkResponseAction traitement des reponses]
     * @return [type] [description]
     */
    public function checkResponseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //$request          = $this->get('request');
        $user = $this->getUser();
        $responseIdsArray = array();
        $responseList = array();
        // var_dump($request->request->get('parameters'));die;
        if ($request->isXmlHttpRequest()) {
            $normalizer = new ObjectNormalizer();
            $encoder = new JsonEncoder();

            $serializer = new Serializer(array($normalizer), array($encoder));

           // $serializer = new Serializer(array($normalizer), array(new JsonEncoder()));
            $parameters = $request->get('parameters');
            //sessionId,responseItemIds,questionId
            extract($parameters);
            $this->saveScore($sessionId, $responseItemIds, $questionId, $qNumber);
            //$nextQuestionIds = $this->incrementQuestionId($questionIds);
            //$responseIds = $this->getResponseFromScore($sessionId, $nextQuestionIds, 'association');
        }
        $nextQuestion = $request->get('sessionQuestion');
      


        $nextQuestionChoice = $em->getRepository('AppBundle:Response')->getReponseByIdQuestion($nextQuestion['id']);

        foreach($nextQuestionChoice as $choice ) {
          $responseList[$choice["id"]] = $choice["content"];
        }
       //$nextQuestion = $serializer->deserialize($nextQuestion, Question::class, 'json');

        $return = array(
            'nextQuestion' => json_encode($nextQuestion),
            'responseIds' => $responseIdsArray,
            'responseList'=>!empty($responseList) ? json_encode($responseList) : [] ,
            'questionIds' => $questionId,
            /* 'countResponse'=>$countResponse,
             'responseCount'=>$responseCount,*/
        );

        $return['responseCode'] = 200;

        return new Response(json_encode($return), $return['responseCode'], array('Content-Type' => 'application/json'));
    }

    /**
     * @param $sessionId
     * @param $response
     * @param $questionIds
     */
    public function saveScore($sessionId, $responseIds, $questionId, $qNumber)
    {

        $today = new \DateTime("now", new \DateTimeZone('Europe/paris'));
        $em = $this->getDoctrine()->getManager();
        $session = $em->getRepository('AppBundle:Evaluation')->find($sessionId);
        $correctAnswer = array();
        $score = $em->getRepository('AppBundle:Score')->findOneBy(array('evaluation' => $sessionId, 'question' => $questionId, 'user' => $this->getUser()->getId()));

        if (empty($score)) {
            $score = new Score();
        } 

        $question = $em->getRepository('AppBundle:Question')->find($questionId);//var_dump($qId);die();
        $correctAnswers = $em->getRepository('AppBundle:Response')->findBy(array('question' => $questionId, 'correct' => 1));
        foreach ($correctAnswers as $ke => $choice) {
            $correctAnswer[] = $choice->getId();
        }
        $userScore = 0;
        if (!empty($responseIds)) {
            foreach ($responseIds as $id) {
                if (in_array($id, $correctAnswer)) {
                    $userScore++;
                }
            }
            $score->setResponseDate($today);
            $score->setStartDate($today);
            $score->setQuestionNumber($qNumber);
            $score->setUser($this->getUser());
            $score->setQuestion($question);
            $score->setEvaluation($session);


            $score->setResponse(json_encode($responseIds));
            $score->setScore($userScore);
            $em->persist($score);
            $em->flush();
        }

    }
}




