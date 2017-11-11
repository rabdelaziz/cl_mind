<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;

class UserController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $securityContext = $this->get('security.authorization_checker');
        $currentUser = $this->getUser();
        if($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            $userRole = $currentUser->getRoles();
            if (in_array('ROLE_ADMIN', $userRole) || in_array('ROLE_SUPER_ADMIN', $userRole)) {
                return $this->redirect($this->generateUrl('adminHomePage'));
            } else if (in_array('ROLE_USER', $userRole)) {
                return $this->redirect($this->generateUrl('userHomePage'));
            } else {
                return $this->redirect('login');
            }
        } else {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Route("session", name="adminHomePage")
     */
    public function adminHomePageAction()
    {
        return $this->render('User/adminHomePage.html.twig');
    }

    /**
     *@Route("user_home_page", name="userHomePage")
     */
    public function userHomePageAction()
    {
        return $this->render('User/sessionInstruction.html.twig');
    }
    
    /**
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
    	
        $em = $this->getDoctrine()->getManager();
        /*
    	$userList = $em->getRepository('AppBundle:User')
    	->findAll();
    	/*
    	$userList = $em->getRepository('AppBundle:User')
    	   ->findBy(['roles' => 'ROLE_CANDIDAT']);
    	*/
        
        /*
    	$userList = $em->getRepository('AppBundle:User')
    	   //->findByRoles('ROLE_ADMIN');
    	->findBy(['roles' => 'ROLE_CANDIDAT']);
    	*/
        $userList = $em->getRepository('AppBundle:User')->findByRoles('ROLE_ADMIN');
    	return $this->render('AppBundle:User:list.html.twig', array(
    			'userList' => $userList,
    	));
    }
    
    /**
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$user = new User();    	
    	$form = $this->createForm(UserType::class, $user);
    	
    	$form->handleRequest($request);
    	if ($form->isSubmitted() && $form->isValid()) {
    		
    		$em = $this->getDoctrine()->getManager();
    		$session = $this->get('session');
    		
    		$user = $form->getData();
    		
    		// Vérifier que l'utilisateur n'est pas connu du référentiel
    		$userFound = $em->getRepository('AppBundle:User')->findUser($user);
    		if (null !== $userFound) {
    			$session->getFlashBag()->add('warning', " Ce utilisateur est connu du référentiel ou utilise un email ou un prénom et nom déjà connus !");
    			return $this->render('AppBundle:User:add.html.twig', array(
    					'form' => $form->createView()
    			));
    		}
    		
    		$user = $this->get('appbundle.user')->generatePassword($user);
    		
    		$em->persist($user);
    		$em->flush();
    		
    		$session->getFlashBag()->add('notice', "L'utilisateur a bien été créé.");
    		
    		return $this->redirectToRoute('user_list');
    	}
    	return $this->render('AppBundle:User:add.html.twig', array(
    		'form' => $form->createView()
    	));
    }
    
    public function editAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
    	 
    	$user = $em->getRepository('AppBundle:User')->find($id);
    	if (null === $user) {
    		throw new NotFoundHttpException("L'utilisateur d'id $id n'existe pas.");
    	}
//$user->removeRole('ROLE_USER');
    	$form = $this->createForm(UserType::class, $user);
    	$form->handleRequest($request);    	//var_dump($form->get('roles')->getData()); die;
    	if ($form->isSubmitted() && $form->isValid()) {
    		$user = $form->getData();
    		
    		return $this->redirectToRoute('user_list');
    	}
    	
    	return $this->render('AppBundle:User:edit.html.twig', array(
    			'form' => $form->createView()
    	));
    }
    
    public function viewAction($id)
    {
    	$user = $this->getDoctrine()->getManager()
    		->getRepository('AppBundle:User')
    		->find($id);
    	if (null === $user) {
    		throw new NotFoundHttpException("L'utilisateur d'id $id n'existe pas.");
    	}

    	return $this->render('AppBundle:User:view.html.twig', array(
    			'user' => $user,
    	));
    }

}
