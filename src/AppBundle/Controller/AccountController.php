<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use AppBundle\Form\ProfileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;


use FOS\UserBundle\FOSUserEvents;

class AccountController extends Controller
{

    /**
     * Modification du profil par l'utilisateur
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createForm(ProfileType::class, $user);


        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                /** @var $userManager UserManagerInterface */
                $userManager = $this->get('fos_user.user_manager');

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

                $userManager->updateUser($user);

                $this->get('session')->getFlashBag()->add('success', "Votre profile a bien été mise à jour.");

                if (null !== $response = $event->getResponse()) {
                    $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
                    return $response;
                }
            } else {
                $this->get('session')->getFlashBag()->add('warning', "Le formulaire n'est pas valide!");
            }
        }

        // Le formulaire de changement du mdp
        $formFactory = $this->get('fos_user.change_password.form.factory');
        $formChangePassword = $formFactory->createForm();
        $formChangePassword->setData($user);

        return $this->render('AppBundle:Account:edit.html.twig', array(
            'form' => $form->createView(),
            'formChangePassword' => $formChangePassword->createView(),
            )
        );
    }

    /**
     *
     * @param Request $request
     * @return jsonResponse
     * @throws AccessDeniedException
     *
     */
    public function ajaxChangePasswordAction(Request $request)
	{
		$jsonResponse	 = new JsonResponse();
		//Tableau de retour json
		$result		 = array(
			'STATUS'	 => true,
			'TYPE'		 => 'success',
			'MESSAGE'	 => '',
			'ERROR'		 => '',
			'ERROR_LINE' => '',
			'DEBUG'		 => array()
		);

		// On teste si c'est un appel Ajax
		if (!$request->isXmlHttpRequest()) {
			$jsonResponse->setStatusCode(400);
			return $jsonResponse->setData(array(
                'MESSAGE' => 'Accès autorisé seulement par Ajax'));
		}

		try {

            $user = $this->getUser();
            if (!is_object($user) || !$user instanceof UserInterface) {
                throw new AccessDeniedException('This user does not have access to this section.');
            }

            /** @var $dispatcher EventDispatcherInterface */
            $dispatcher = $this->get('event_dispatcher');

            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            /** @var $formFactory FactoryInterface */
            $formFactory = $this->get('fos_user.change_password.form.factory');

            $form = $formFactory->createForm();
            $form->setData($user);

            $form->handleRequest($request);

            if ($form->isSubmitted()) {

                if ($form->isValid()) {

                    /** @var $userManager UserManagerInterface */
                    $userManager = $this->get('fos_user.user_manager');

                    $event = new FormEvent($form, $request);
                    $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);

                    $userManager->updateUser($user);

                    if (null === $response = $event->getResponse()) {
                        $url = $this->generateUrl('fos_user_profile_show');
                        $response = new RedirectResponse($url);
                    }

                    $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                     $result['TYPE'] = 'success';
                    $result['MESSAGE'] = 'Le mot de passe a bien été modifié.';

                } else {
                    $result['TYPE'] = 'warning';
                    $result['MESSAGE'] = 'Le formulaire est invalide.';
                }

                $html = $this->renderView('AdminBundle:Admin:Account/password_change.html.twig', array(
                    'formChangePassword' => $form->createView(),
                    )
                );
                $result['form'] = $html;
            }
		} catch (\Exception $e) {
            $result['form'] = $form;
//			$result['STATUS']	 = false;
//			$result['TYPE']		 = 'error';
//			if ($e instanceof \InvalidArgumentException || $e instanceof \UnexpectedValueException) {
//				$response->setStatusCode(400);
//				$result['MESSAGE'] = $e->getMessage();
//			} else {
//				$response->setStatusCode(500);
//				$result['MESSAGE']	 = $this->getTranslator()->trans('common.ajax.messages.error_load', array(), 'common');
//				$result['ERROR']	 = (string) $e;
//			}
//			$result['ERROR_LINE'] = $e->getLine();
		}

		return $jsonResponse->setData($result);
	}
}
