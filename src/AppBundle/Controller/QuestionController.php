<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\QuestionType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Response;


class QuestionController extends Controller
{
    /**
     *  Liste des questions
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function indexAction()
    {
        $listQuestions = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Question')->getQuestionsOrderByTopic('DESC');

        return $this->render('AppBundle:Question:index.html.twig', array(
            'listQuestions' => $listQuestions,
        ));
    }

    /**
     * 
     * @param int $id
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $question = $em->getRepository('AppBundle:Question')->find($id);
        if (null === $question) {
            throw new NotFoundHttpException("La question d'id $id n'existe pas.");
        }

        $form = $this->createForm(QuestionType::class, $question);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

        	$em->flush();
        	$this->get('session')->getFlashBag()->add('notice', "La question a bien été mise à jour.");
        	
        	return $this->redirectToRoute('question_index');
        }
        
        return $this->render('AppBundle:Question:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * 
     * @param int $id
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($id)
    {
        $question = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Question')->getQuestionById($id);
        
        if (null === $question) {
            throw new NotFoundHttpException("La question d'id $id n'existe pas.");
        }
        
        return $this->render('AppBundle:Question:view.html.twig', array(
            'question' => $question,
        ));
    }

    /**
     * 
     * @param int $id
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$session = $this->get('session');

    	$question = $em->getRepository('AppBundle:Question')->find($id);
    	if (null === $question) {
    		throw new NotFoundHttpException("La question d'id $id n'existe pas.");
    	}

    	if ($this->get('appbundle.question')->canBeDelete($question)) {
    		$em->remove($question);
    		$em->flush();
    	
    		$session->getFlashBag()->add('notice', "la question a bien été supprimée ainsi ques les réponses associées.");
    	} else {
    		$session->getFlashBag()->add('warning', 'Cette question ne peut être supprimée. Mais vous pourrez la désactiver.');
    	}
    	 
    	return $this->redirectToRoute('question_index');
    }

    /**
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {        
        $question = new Question();
       // $question->addResponse(new Response());
        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $question = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('notice', "la question a bien été créée.");
            
            return $this->redirectToRoute('question_index');
        } else {
            $this->get('session')->getFlashBag()->add('warning', "Le formulaire n'est pas valide!");
        }

        return $this->render('AppBundle:Question:add.html.twig', array(
            'form' => $form->createView()
        ));

    }

}
