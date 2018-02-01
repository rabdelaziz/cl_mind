<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;


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
                return $this->redirect($this->generateUrl('evaluation_index'));
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
     * @param Request $request
     * @param $data
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */

    public function connectViaSSOAction(Request $request, $data)
    {
        // Initializing Entity Manager
        $em = $this->getDoctrine()->getManager();
        $manageCandidate = $this->get('appBundle.manage.candidate.evaluation');
        $username = $manageCandidate->makeSSODataConnection($data, 'd');

        // Get user
        $user = $em->getRepository("AppBundle:User")->findOneBy(['username' => $username]);

        if ($user instanceof User) {
            // Ici, on va connecter l'utilisateur ! :)
            $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
            $this->get("security.token_storage")->setToken($token);

            // On informe qu'une connexion vien d'avoir lieu aux events.
            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

            // Un redirection vers la route "homepage"
            return $this->redirect($this->generateUrl('userHomePage'));
        } else {
            throw $this->createNotFoundException();
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
     * @Security("has_role('ROLE_CANDIDAT')")
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

        $userList = $em->getRepository('AppBundle:User')->findOnlyUsers();
    	return $this->render('AppBundle:User:list.html.twig', array(
            'userList' => $userList,
            'linkUserListingOn' => true
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
    		
    		$session->getFlashBag()->add('success', "L'utilisateur a bien été créé.");

    		return $this->redirectToRoute('user_list');
    	}

    	return $this->render('AppBundle:User:add.html.twig', array(
    		'form' => $form->createView(),
            'linkUserAddOn' => true
    	));
    }
    
    public function editAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
    	 
    	$user = $em->getRepository('AppBundle:User')->find($id);
    	if (null === $user) {
    		throw new NotFoundHttpException("L'utilisateur d'id $id n'existe pas.");
    	}

    	$form = $this->createForm(UserType::class, $user);
    	$form->handleRequest($request);
    	if ($form->isSubmitted() && $form->isValid()) {

            $user->setUpdatedAt(new \DateTime('now'));
            $em->flush();
            $session = $this->get('session');
            $session->getFlashBag()->add('success', "L'utilisateur a bien été créé.");
    		return $this->redirectToRoute('user_list');
    	}
    	
    	return $this->render('AppBundle:User:edit.html.twig', array(
    			'form' => $form->createView(),
                'linkUserEditOn'=>true,
                'user'=> $user
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
                'linkUserViewOn' =>true,
    	));
    }

}

