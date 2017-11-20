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

use AppBundle\Entity\Evaluation;
use AppBundle\Entity\User;
use AppBundle\Form\EvaluationType;
use AppBundle\Entity\Difficulty;
use AppBundle\Form\EvaluationStatusType;
use AppBundle\Form\ContactType;


class EvaluationController extends Controller
{
    /**
     * @Route("evaluation/new_topic", name="create_topic", options={"expose"=true})
     */
    public function createTopicAction(Request $request)
    {
        $form = $this->createForm(TopicType::class);
        $form->handleRequest($request);

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
    public function startTestAction(Request $request)
    {
        $manageEvaluation = $this->get('appBundle.manage.candidate.evaluation');
        $em = $this->getDoctrine()->getManager();
        $startDate = new \DateTime("now", new \DateTimeZone('Europe/paris'));
        $now = $startDate->format('Y-m-d H:i:s');
        $session = $this->get('session');
        $currentUser = $this->getUser();
        $currentQuestionStartDate = [];
        $questionNumber  = $questionTimer = 0;
        $currentEvaluation = $em->getRepository('AppBundle:Evaluation')->getCurrentEvaluation($currentUser->getId());

        $questionsList = $currentEvaluation->getQuestions()->toArray();
        $serializedQuestionsList = $manageEvaluation->getSerializedQuestionsList($questionsList);
        //pour les pref recuperer la derniere seulement
        $score = $em->getRepository('AppBundle:Score')->findOneBy(  array('evaluation' => $currentEvaluation->getId(),
                                                                        'user' => $currentUser->getId()),
                                                                    array('id' => 'DESC')
                                                               );
        //var_dump($score ,count($questionsList));die;
        //test si le test est terminé

        $session->set('countQuestion', count($questionsList));
        if (!empty($score)) {
            $questionNumber = $score->getQuestionNumber();
            if ($questionNumber + 1 === count($questionsList)) {
                return $this->redirectToRoute('evaluation_result', array());
            }
            $questionNumber++;
            $session->set('validateQuestionNumber', $questionNumber);
        } else {
            $session->set('validateQuestionNumber', 0);
        }

       // var_dump($questionsList->toArray(),$questionNumber);die;
        $firstQuestion =  $questionsList[$questionNumber];

        if ($session->get('currentQuestionStartDate') === null) {
            $currentQuestionStartDate[$firstQuestion->getId()] = $startDate;
            $session->set('currentQuestionStartDate', $currentQuestionStartDate);
            $questionTimer = $firstQuestion->getDuration();
        } else {
          //  var_dump($firstQuestion);die;
            $startedDateInSession = $session->get('currentQuestionStartDate')[$firstQuestion->getId()];
            $startedDateAsString = $startedDateInSession->format('Y-m-d H:i:s');
            $timerDiff = strtotime($now) - strtotime($startedDateAsString);
        }

        if($request->isXmlHttpRequest() && isset ($timerDiff)){
            //var_dump($timerDiff);die;
            if($timerDiff >  $firstQuestion->getDuration()) {
                $manageEvaluation->saveScore($session->getId(), [], $firstQuestion->getId(), $questionNumber);
                $questionNumber++;
                if ($questionNumber === count($questionsList)) {
                    return $this->redirectToRoute('evaluation_result', array()); 
                }
                $firstQuestion = $questionsList[$questionNumber];
                $questionTimer =  $firstQuestion->getDuration();
            } else {
                $questionTimer = $firstQuestion->getDuration() - $timerDiff;
            }
        }

        return $this->render('Evaluation/evaluation.html.twig', [
            'sId' => $currentEvaluation->getId(),
            'firstQuestion' => $firstQuestion,
            'questionNumber' => $questionNumber,
            'validQuestionNumber' => $session->get('validateQuestionNumber'),
            'questionTimer' => $questionTimer * 60,
            'totalQuestionNumber'=>$session->get('countQuestion'),
            'sessionQuestion' => json_decode($serializedQuestionsList)]);
    }

    /**
     * @Route("user/evaluation/started", name="checkQuestionResponse")
     */
    public function checkResponseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $manageEvaluation = $this->get('appBundle.manage.candidate.evaluation');
        $session = $this->get('session');
        $responseIdsArray = array();
        $responseList = array();
        $startedDate = new \DateTime("now", new \DateTimeZone('Europe/paris'));
        //$now = $startedDate->format('Y-m-d H:i:s');

        if ($request->isXmlHttpRequest()) {
            $parameters = $request->get('parameters');
            //sessionId,responseItemIds,questionId
            extract($parameters);
            $responseItemIds = isset($parameters['responseItemIds']) ? $parameters['responseItemIds'] : [];
            
            
            $manageEvaluation->saveScore($sessionId, $responseItemIds, $questionId, $qNumber, $this->getUser());
            $session->set('validateQuestionNumber', $session->get('validateQuestionNumber') + 1 );
            if ($session->get('validateQuestionNumber') !=  $session->get('countQuestion')) {
                $nextQuestion = $request->get('sessionQuestion');
                $nextQuestionChoice = $em->getRepository('AppBundle:Response')->getReponseByIdQuestion($nextQuestion['id']);

                foreach($nextQuestionChoice as $choice ) {
                    $responseList[$choice["id"]] = $choice["content"];
                }
                $currentQuestionStartDate[$nextQuestion['id']] = $startedDate;
                $session->set('currentQuestionStartDate', $currentQuestionStartDate);
            }/*else {
                return $this->redirectToRoute('evaluation_result', array());
            }*/
           
        }

        $return = array(
            'nextQuestion' => isset($nextQuestion) ? json_encode($nextQuestion) : [],
            'responseIds' => $responseIdsArray,
            'responseList'=>!empty($responseList) ? json_encode($responseList) : [] ,
            'questionIds' => $questionId,
            'validQuestionNumber' => $session->get('validateQuestionNumber'),
            'questionNumber'=> $session->get('countQuestion') - $session->get('validateQuestionNumber'),
            'totalQuestionNumber'=>$session->get('countQuestion')

        );

        $return['responseCode'] = 200;

        return new Response(json_encode($return), $return['responseCode'], array('Content-Type' => 'application/json'));
    }

    
    /**
     * @Route("user/evaluation/result", name="evaluation_result", options={"expose"=true})
     */
    public function displayResultPageAction()
    {
        return $this->render('Evaluation/evaluation-result.html.twig');
    }

    /**
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Evaluation');

        $evaluationList = $repository->findAll();

        $evaluationService = $this->get('appbundle.evaluation');
        foreach ($evaluationList as $evaluation) {
            // Liste des noms des thèmes utilisés dans l'évaluation
            $topics = $evaluationService->getDistinctTopicsName($evaluation);
            $evaluation->themes = implode('|', $topics);
        }

        return $this->render('AppBundle:Evaluation:index.html.twig', array(
            'evaluationList' => $evaluationList,
        ));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $evaluation = new Evaluation();

        // L'auteur de la création de l'evaluation
        $author = $this->getUser();
        $evaluation->setAuthor($author);

        $form = $this->createForm(EvaluationType::class, $evaluation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $session = $request->getSession();
            $evaluation = $form->getData();

            // Liste de tous les niveaux de difficulté pour les questions
            $levelList = $em->getRepository('AppBundle:Level')
                ->findAll();

            // Générer toutes les questions pour l'évaluation
            $evaluationService = $this->get('appbundle.evaluation');
            $evaluation = $evaluationService->generateQuestions($evaluation, $form->get('topics')->getData(), $levelList, $request->get('question_numbers'), $form->get('difficulty')->getData());

            $em->persist($evaluation);
            $em->flush();

            $session->getFlashBag()->add('notice', "l'évaluation a bien été créée.");

            return $this->redirectToRoute('evaluation_index');
        }

        return $this->render('AppBundle:Evaluation:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     *
     * @param Request $request
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $evaluation = $em->getRepository('AppBundle:Evaluation')->getEvaluationById($request->get('id'));
        if (null === $evaluation) {
            throw new NotFoundHttpException("Le test d'id " . $request->get('id') . " n'existe pas.");
        }

        // Formulaire pour changer le statut (activer, désactiver, archiver) de l'évaluation
        $form = $this->createForm(EvaluationStatusType::class, $evaluation);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $evaluation = $form->getData();

                $em->persist($evaluation);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Le status a bien été modifié.');
            }
        }

        // Liste des noms des thèmes utilisés dans l'évaluation
        $topics = $this->get('appbundle.evaluation')->getDistinctTopicsName($evaluation);

        $evaluation->themes = implode('|', $topics);


        $nbCandidates = $em->getRepository('AppBundle:User')->getNbByEvaluationId($request->get('id'));

        return $this->render('AppBundle:Evaluation:view.html.twig', array(
            'evaluation' => $evaluation,
            'nbCandidates' => $nbCandidates,
            'form' => $form->createView()
        ));
    }

    /**
     * Permet d'ajouter un candidat
     *
     * @param Request $request
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addCandidateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $evaluation = $em->getRepository('AppBundle:Evaluation')->find($request->get('id'));
        if (null === $evaluation) {
            throw new NotFoundHttpException("Le test d'id " . $request->get('id') . " n'existe pas.");
        }

        $candidate = new User();
        ////$candidate->addEvaluation($evaluation);

        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $session = $request->getSession();
            $candidate = $form->getData();

            $userService = $this->get('appbundle.user');
            $userFound = $userService->findUser($candidate);

            if (false === $userFound) {
                $session->getFlashBag()->add('warning', "Il existe un candidat avec le même nom et prénom ou qui utilise le même email!");
                return $this->render('AppBundle:Evaluation:edit.html.twig', array(
                    'form' => $form->createView()
                ));
            } elseif ($userFound instanceof User) {
                // Si le candidat est déjà connu => on lui envoie juste le lien de cette évaluation
                // 1 => On vérifie s'il n'as pas passé le même test




            } else { //$userFound === null
                // Si nouveau candidat
                $candidate = $userService->generateUserData($candidate, array('ROLE_CANDIDAT'));
                $evaluation->addCandidate($candidate);

                $em->persist($evaluation);
                $em->flush();

                $session->getFlashBag()->add('notice', 'Le candidat a bien été affecté au test.');
            }

            return $this->redirectToRoute('evaluation_index');
        }

        return $this->render('AppBundle:Evaluation:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }


    /**
     *
     * @param int $id
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deteteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');

        $evaluation = $em->getRepository('AppBundle:Evaluation')
            ->find($id);
        if (null === $evaluation) {
            throw new NotFoundHttpException("Le test d'id $id n'existe pas.");
        }

        $evaluationService = $this->get('appbundle.evaluation');
        if ($evaluationService->canBeDelete($evaluation)) {
            $em->remove($evaluation);
            $em->flush();

            $session->getFlashBag()->add('notice', "l'évaluation a bien été supprimée.");
        } else {
            $session->getFlashBag()->add('warning', 'Cette évaluation ne peut être supprimer.');
        }

        return $this->redirectToRoute('evaluation_index');
    }

}

