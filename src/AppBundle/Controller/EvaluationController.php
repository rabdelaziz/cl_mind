<?php

/**
 * Created by PhpStorm.
 * User: ramzi
 * Date: 26/05/2016
 * Time: 22:52
 */

namespace AppBundle\Controller;
use AppBundle\Entity\Repository;
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
use AppBundle\Form\CandidateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class EvaluationController extends Controller
{
    /**
     * @Route("evaluation/start", name="start_evaluation")
     * @Security("has_role('ROLE_CANDIDAT')")
     */
    public function startTestAction(Request $request)
    {
        $now = new \DateTime("now");
        $manageEvaluation = $this->get('appBundle.manage.candidate.evaluation');
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');
        $user = $this->getUser();
        $questionNumber  = $questionTimer = 0;
        $currentEvaluation = $em->getRepository('AppBundle:Evaluation')->getCurrentEvaluation($user->getId());
        $session->set('evaluationId', $currentEvaluation->getId());
        
        $questionsList = $currentEvaluation->getQuestions()->toArray();
        $serializedQuestionsList = $manageEvaluation->getSerializedQuestionsList($questionsList);
        
        $score = $em->getRepository('AppBundle:Score')->findBy([
                                                                    'evaluation' => $currentEvaluation->getId(),
                                                                    'user' => $user->getId()],
                                                                   /* 'expired' => false*/
                                                                    ['id' => 'DESC'],
                                                                         2
                                                                           );

        $session->set('countQuestion', count($questionsList));

        //test déja commencé
        if (!empty($score)) {
            $lastScore = current($score);
            $questionNumber = $lastScore->getQuestionNumber();
            $currentQuestion =  $questionsList[$questionNumber];
            if ($questionNumber + 1 === count($questionsList) && $lastScore->getExpired()) {
                return $this->redirectToRoute('evaluation_result');
            }
            if(!$lastScore->getExpired()) {
                $remainingTime = $manageEvaluation->getDiffDateTime($lastScore->getStartDate(), $now);
                if ($remainingTime  < $currentQuestion->getDuration()) {
                    $questionTimer =   $currentQuestion->getDuration() - $remainingTime;
                    $session->set('validateQuestionNumber', $questionNumber);
                } else {
                    $manageEvaluation->updateScore( $currentEvaluation->getId(), [], $currentQuestion->getId(), $questionNumber, $this->getUser());

                    if ($questionNumber + 1 === count($questionsList)) {
                        return $this->redirectToRoute('evaluation_result');
                    }
                    $questionNumber++;
                    $session->set('validateQuestionNumber', $questionNumber);
                    $currentQuestion =  $questionsList[$questionNumber];
                    $questionTimer =  $currentQuestion->getDuration();
                    $manageEvaluation->saveScore($currentEvaluation->getId(), $currentQuestion->getId(), $questionNumber, $user);
                }
            }

        } else {//aucune question na été vu

            $currentQuestion =  $questionsList[$questionNumber];
            $questionTimer =  $currentQuestion->getDuration();
            $manageEvaluation->saveScore($currentEvaluation->getId(), $currentQuestion->getId(), $questionNumber, $user);

            $session->set('validateQuestionNumber', 0);
        }


        return $this->render('Evaluation/evaluation.html.twig', [
            'sId' => $currentEvaluation->getId(),
            'firstQuestion' => $currentQuestion,
            'questionNumber' => $questionNumber,
            'validQuestionNumber' => $session->get('validateQuestionNumber'),
            'questionTimer' => $questionTimer,
            'totalQuestionNumber'=>$session->get('countQuestion'),
            'sessionQuestion' => json_decode($serializedQuestionsList)]);
    }

    /**
     * @Route("user/evaluation/started", name="checkQuestionResponse")
     * @Security("has_role('ROLE_CANDIDAT')")
     */
    public function checkResponseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $manageEvaluation = $this->get('appBundle.manage.candidate.evaluation');
        $session = $this->get('session');
        $responseIdsArray = array();
        $responseList = array();
        //$startedDate = new \DateTime("now", new \DateTimeZone('Europe/paris'));
        //$now = $startedDate->format('Y-m-d H:i:s');

        if ($request->isXmlHttpRequest()) {
            $parameters = $request->get('parameters');
            //sessionId,responseItemIds,questionId
            extract($parameters);
            $responseItemIds = isset($parameters['responseItemIds']) ? $parameters['responseItemIds'] : [];
            //var_dump($qNumber);die;

            $manageEvaluation->updateScore($sessionId, $responseItemIds, $questionId, $qNumber, $this->getUser());
            $session->set('validateQuestionNumber', $session->get('validateQuestionNumber') + 1 );
            if ($session->get('validateQuestionNumber') !=  $session->get('countQuestion')) {
                $nextQuestion = $request->get('sessionQuestion');
                $nextQuestionChoice = $em->getRepository('AppBundle:Response')->getReponseByIdQuestion($nextQuestion['id']);

                foreach($nextQuestionChoice as $choice ) {
                    $responseList[$choice["id"]] = $choice["content"];
                }
                $qNumber= $qNumber + 1;
                $manageEvaluation->saveScore($sessionId, $nextQuestion['id'],$qNumber , $this->getUser());
              //  $currentQuestionStartDate[$nextQuestion['id']] = $startedDate;
             //   $session->set('currentQuestionStartDate', $currentQuestionStartDate);
            }/*else {
                return $this->forward('AppBundle:Evaluation:displayResultPage', array(

                ));

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
            'totalQuestionNumber'=> $session->get('countQuestion')

        );

        $return['responseCode'] = 200;

        return new Response(json_encode($return), $return['responseCode'], array('Content-Type' => 'application/json'));
    }


    /**
     * @Route("user/evaluation/result", name="evaluation_result", options={"expose"=true})
     * @Security("has_role('ROLE_CANDIDAT')")
     */
    public function displayResultPageAction(Request $request)
    {
        $session = $this->get('session');
        $currentEvaluation = $this->getDoctrine()->getRepository('AppBundle:Evaluation')->findOneBy(['id' => $this->get('session')->get('evaluationId')]);
        $manageEvaluation = $this->get('appBundle.manage.candidate.evaluation');
        $score = $manageEvaluation->getFinalScoreByEvaluation($this->getUser(), $currentEvaluation);
        //var_dump($score);die;
        if ($session->get('countQuestion')) {
            $totalPoint = $session->get('countQuestion');
        } else {
            $totalPoint = count($currentEvaluation->getQuestions()->toArray());
        }

        //gereration du rapport de resultat todo ajout de test pour assurer un seul envoie
        $path = $request->server->get('DOCUMENT_ROOT').$request->getBasePath() . '/reports';
        $manageEvaluation->sendResultReport($this->getUser(), $currentEvaluation, $path);



        return $this->render('Evaluation/evaluation-result.html.twig', [
                                                                                'score' => $score,
                                                                                'totalPoint' => $totalPoint
                                                                            ]);
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
            'linkEvaluationListingOn' => true

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

            $session->getFlashBag()->add('success', "l'évaluation a bien été créée.");

            return $this->redirectToRoute('evaluation_index');
        }

        return $this->render('AppBundle:Evaluation:add.html.twig', array(
            'form' => $form->createView(),
            'linkEvaluationAddOn' => true,
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

                $request->getSession()->getFlashBag()->add('success', 'Le status a bien été modifié.');
            }
        }

        // Liste des noms des thèmes utilisés dans l'évaluation
        $topics = $this->get('appbundle.evaluation')->getDistinctTopicsName($evaluation);

        $evaluation->themes = implode('|', $topics);


        $nbCandidates = $em->getRepository('AppBundle:User')->getNbByEvaluationId($request->get('id'));

        return $this->render('AppBundle:Evaluation:view.html.twig', array(
            'evaluation' => $evaluation,
            'nbCandidates' => $nbCandidates,
            'form' => $form->createView(),
            'linkEvaluationEditOn' => true

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
        $sendEmail = true;
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
                return $this->render('AppBundle:Evaluation:candidate.html.twig', array(
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

                $session->getFlashBag()->add('success', 'Le candidat a bien été affecté au test.');
                //TODO rendre ce cette variable dynamique en ajoutant le choix sur le form
                if($sendEmail) {
                    $manageCandidate = $this->get('appBundle.manage.candidate.evaluation');
                    $manageCandidate->sendLinkEvaluation($candidate);
                }
            }

            return $this->redirectToRoute('evaluation_index');
        }

        return $this->render('AppBundle:Evaluation:candidate.html.twig', array(
            'form' => $form->createView(),
            'linkCandidatAddOn' => true,
            'evaluation' => $evaluation
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

            $session->getFlashBag()->add('success', "l'évaluation a bien été supprimée.");
        } else {
            $session->getFlashBag()->add('warning', 'Cette évaluation ne peut être supprimer.');
        }

        return $this->redirectToRoute('evaluation_index');
    }

}

