<?php
/**
 * Created by PhpStorm.
 * User: ramzi
 * Date: 26/05/2016
 * Time: 22:52
 */

namespace AppBundle\Controller;

use AppBundle\Form\TopicType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class SessionController extends Controller
{
    /**
     * @Route("session/new_topic", name="create_topic", options={"expose"=true})
     */
    public  function createTopicAction(Request $request)
    {
    // die('t');
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
            $response->setData(['status' => 'ok', 'msg' => utf8_encode("Vos modification ont été bien enregistrés"), 'topic' => $topic],  200);
            return $response;
          //  return new JsonResponse(['status' => 'ok', 'msg' => "Vos modification ont été bien enregistrés", 'topic' => $topic],  200);
//            return $this->redirectToRoute('topic_list');
        }
        return $this->render('forms/topic/create_topic.html.twig', [
            'form' => $form->createView(),
        ]);

       // return new JsonResponse(['status' => 'ok', 'template' => $template]);
    }

    /**
     * @Route("session/topic_list", name="topic_list")
     */
    public  function topicListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sessionList = $em->getRepository('AppBundle:Topic')->findAll();

        return $this->render('topic/topic_liste.html.twig', array('sessionList' => $sessionList,
            'activateItem' => 1,
        ));

    }
}




