<?php
/**
 * Created by PhpStorm.
 * User: ram
 * Date: 30/01/18
 * Time: 23:10
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Repository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\TopicType;;

class TopicController extends Controller
{
    /**
     * @Route("topic", name="create_topic")
     */
    public function createTopicAction(Request $request)
    {
        $form = $this->createForm(TopicType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $this->get('session');
            /** @var \AppBundle\Entity\Topic $topic */
            $topic = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($topic);
            $em->flush();

            $session->getFlashBag()->add('success', "Le thème a bien été créé");

            return $this->redirectToRoute('topic_list');
        }

        return $this->render('AppBundle:Topic:add.html.twig', [
            'form' => $form->createView(),
            'linkTopicAddOn' => true,
        ]);
    }

    /**
     * @Route("topic/list", name="topic_list")
     */
    public function topicListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $topicList = $em->getRepository('AppBundle:Topic')->findAll();

        return $this->render('AppBundle:Topic:index.html.twig',
            [
                'topicList' => $topicList,
                'linkTopicListingOn' => true

            ]
        );
    }

    /**
     * @Route("topic/edit/{id}", name="edit_topic")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $topic = $em->getRepository('AppBundle:Topic')->find($id);
        if (null === $topic) {
            throw new NotFoundHttpException("Le thème d'id $id n'existe pas.");
        }

        $form = $this->createForm(TopicType::class, $topic);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Le thème a bien été mis à jour.");

            return $this->redirectToRoute('topic_list');
        }

        return $this->render('AppBundle:Topic:edit.html.twig', array(
            'form' => $form->createView(),
            'linkTopicEditOn' => true,
            'topic' => $topic
        ));
    }
}