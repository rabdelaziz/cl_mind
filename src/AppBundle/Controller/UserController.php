<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
     * @Route("session_instruction", name="adminHomePage")
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

}
