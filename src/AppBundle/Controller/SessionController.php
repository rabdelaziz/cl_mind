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
use Symfony\Component\HttpFoundation\Request;


class SessionController extends Controller
{
    /**
     * @Route("session/new_topic", name="create_topic")
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

            return $this->redirectToRoute('topic_list');
        }
        return $this->render('topic/create_topic.html.twig', [
            'form' => $form->createView(),
        ]);
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




