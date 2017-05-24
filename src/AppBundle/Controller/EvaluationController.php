<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Evaluation;
use AppBundle\Entity\User;
use AppBundle\Form\EvaluationType;
use AppBundle\Entity\Difficulty;
use AppBundle\Form\EvaluationStatusType;
use AppBundle\Form\ContactType;

class EvaluationController extends Controller
{
    /**
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function indexAction()
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
    	$evaluation->addCandidate(new User());
    	
    	
    	// A remplacer par les données de session
    	$author = $em->getRepository('AppBundle:User')->findByUsername('testUser')[0];
    	$evaluation->setAuthor($author);
    	
    	    	
    	$form = $this->createForm(EvaluationType::class, $evaluation);    	

    	$form->handleRequest($request);
    	if ($form->isSubmitted() && $form->isValid()) { 		
    		
    		$session = $request->getSession();
    		$evaluation = $form->getData();
    		
    		// Le nouveau candidat
    		$newCandidate = $evaluation->getCandidates()[0];
    		// Vérifier si le candidat existe
    		$candidate = $em->getRepository('AppBundle:User')
    			->findUser($newCandidate);

    		// Rétirer le candidat car il manque certaines informations obligatoires (comme le mdp)
    		$evaluation->removeCandidate($newCandidate);
    		if (null !== $candidate) {   				
    			if ($candidate->getEmail() != $newCandidate->getEmail()
    					|| $candidate->getFirstName() != $newCandidate->getFirstName()
    					|| $candidate->getLastName() != $newCandidate->getLastName()
    			) {
    				$session->getFlashBag()->add('warning', "Un autre candidat utilise l'une de ces informations!");
    			
    				return $this->render('AppBundle:Evaluation:add.html.twig', array(
    						'form' => $form->createView()
    				));
    			}
    			
    			$newCandidate = $candidate;    			
    		} else {
    			// c'est un nouveau candidat => générer les informations manquantes
    			$userService = $this->get('appbundle.user');
    			$newCandidate = $userService->generateUserData($newCandidate);
    		}
    		// Rajouter le candidat
    		$evaluation->addCandidate($newCandidate);
    		
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
     * @param int $id
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();

    	$evaluation = $em->getRepository('AppBundle:Evaluation')
    		->find($id);
    	if (null === $evaluation) {
    		throw new NotFoundHttpException("Le test d'id $id n'existe pas.");
    	}

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
    	
    	return $this->render('AppBundle:Evaluation:view.html.twig', array(
    			'evaluation' => $evaluation,
    			'form' => $form->createView()
    	));
    }
    
    /**
     * Permet d'ajouter un candidat
     * 
     * @param Request $request
     * @param int $id
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$evaluation = $em->getRepository('AppBundle:Evaluation')->find($id);    		
    	if (null === $evaluation) {
    		throw new NotFoundHttpException("Le test d'id $id n'existe pas.");
    	}
    	
    	$user = new User();
    	$user->addEvaluation($evaluation);    	
    	
    	$form = $this->createForm(ContactType::class, $user);
    	$form->handleRequest($request);
    	if ($form->isSubmitted() && $form->isValid()) {
    		$session = $request->getSession();
    		$user = $form->getData();
    		
    		$userService = $this->get('appbundle.user');
    		$userFound = $userService->findUser($user);
    		
    		if (false === $userFound) {
    			$session->getFlashBag()->add('warning', "Un autre candidat utilise l'une de ces informations!");
    			return $this->render('AppBundle:Evaluation:edit.html.twig', array(
    					'evaluation' => $evaluation,
    					'form' => $form->createView()
    					));
    		} elseif ($userFound instanceof User) {
    			if ($userFound->getEvaluations()->contains($evaluation)) {
    				// Un candidat ne doit pas participer au même test plus d'1 fois
    				$session->getFlashBag()->add('warning', "Ce candidat a déja été évalué par ce test!");
    				return $this->render('AppBundle:Evaluation:edit.html.twig', array(
    						'evaluation' => $evaluation,
    						'form' => $form->createView()
    				));    			
    			} else {
    				// Le candidat est connu mais n'a jamais participé à ce test
    				$evaluation->addCandidate($userFound);
    			}    			
    		} else { //$userFound === null
    			$user = $userService->generateUserData($user);
    			$evaluation->addCandidate($user);
    		}    		    		

    		$em->persist($evaluation);
    		$em->flush();
    			 
    		$session->getFlashBag()->add('notice', 'Le candidat a bien été affecté au test.');
    			
    		return $this->redirectToRoute('evaluation_index');    	
    	}
    	 
    	return $this->render('AppBundle:Evaluation:edit.html.twig', array(
    			'evaluation' => $evaluation,
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
