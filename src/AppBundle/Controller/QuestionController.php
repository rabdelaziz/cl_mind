<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Question;
use AppBundle\Entity\Reponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\QuestionType;
use Symfony\Component\HttpFoundation\Request;
class QuestionController extends Controller
{
    public function indexAction()
    {

        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Question');

        $listQuestions = $repository->findAll();

        return $this->render('AppBundle:Question:index.html.twig', array(
            'listQuestions' => $listQuestions,
        ));
    }

    public function editAction($id)
    {
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Question');

        $question = $repository->find($id);
        if (null === $question) {
            throw new NotFoundHttpException("La question d'id $id n'existe pas.");
        }

        $form = $this->createForm(QuestionType::class, $question);
        return $this->render('AppBundle:Question:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function viewAction($id)
    {
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Question');

        $question = $repository->find($id);
        if (null === $question) {
            throw new NotFoundHttpException("La question d'id $id n'existe pas.");
        }
        return $this->render('AppBundle:Question:view.html.twig', array(
            'question' => $question,
        ));
    }

    public function deleteAction($id)
    {

    }

    public function addAction(Request $request)
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $question = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
            $em->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('AppBundle:Question:add.html.twig', array(
            'form' => $form->createView()
        ));

    }

}
