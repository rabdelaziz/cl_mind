<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Form\CandidateType;

class CandidateController extends Controller
{

    /**
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $candidatesList = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:User')->findByRole('ROLE_CANDIDAT');

        return $this->render('AppBundle:Candidate:index.html.twig', array(
            'candidatesList' => $candidatesList,
        ));
    }
    
    public function editAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
    	 
    	$candidate = $em->getRepository('AppBundle:User')->find($id);
    	if (null === $candidate) {
    		throw new NotFoundHttpException("Le candidat d'id $id n'existe pas.");
    	}

    	$form = $this->createForm(CandidateType::class, $candidate);
    	$form->handleRequest($request);
    	if ($form->isSubmitted() && $form->isValid()) {

            $candidate->setUpdatedAt(new \DateTime('now'));
            
    		$em->flush();
            
            $session->getFlashBag()->add('notice', "le candidat a bien été créé.");
            
    		return $this->redirectToRoute('candidate_index');
    	}
    	
    	return $this->render('AppBundle:Candidate:edit.html.twig', array(
    			'form' => $form->createView()
    	));
    }
    
    public function viewAction($id)
    {
        $candidate = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:User')->find($id);
        
    	if (null === $candidate) {
    		throw new NotFoundHttpException("Le candidat d'id $id n'existe pas.");
    	}

    	return $this->render('AppBundle:User:view.html.twig', array(
    			'candidate' => $candidate,
    	));
    }

}