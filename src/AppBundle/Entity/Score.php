<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Score
 *
 * @ORM\Table(name="score")
 * @ORM\Entity
 */
class Score
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @var User $user
    *
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="user")
    * 
    */
    private $user;

      /**
    * @var Question $question
    *
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Question", inversedBy="question")
    * 
    */
    private $question;

        /**
    * @var Evaluation $evaluation
    *
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Evaluation", inversedBy="evaluation")
    * 
    */
    private $evaluation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="response_date", type="datetime")
     */
    private $responseDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="score", type="integer")
     */
    private $score;

    /**
     * @var string
     *
     * @ORM\Column(name="question_number", type="integer", length=2)
     */
    private $questionNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="response", type="string", length=255)
     */
    private $response;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set responseDate
     *
     * @param \DateTime $responseDate
     * @return Score
     */
    public function setResponseDate($responseDate)
    {
        $this->responseDate = $responseDate;

        return $this;
    }

    /**
     * Get responseDate
     *
     * @return \DateTime 
     */
    public function getResponseDate()
    {
        return $this->responseDate;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Score
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set score
     *
     * @param integer $score
     * @return Score
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set questionNumber
     *
     * @param string $questionNumber
     * @return Score
     */
    public function setQuestionNumber($questionNumber)
    {
        $this->questionNumber = $questionNumber;

        return $this;
    }

    /**
     * Get questionNumber
     *
     * @return string 
     */
    public function getQuestionNumber()
    {
        return $this->questionNumber;
    }

    /**
     * Set response
     *
     * @param string $response
     * @return Score
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response
     *
     * @return string 
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set user
     *
     * @param AppBundle\Entity\User $user
     * @return Score
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set question
     *
     * @param AppBundle\Entity\Question $question
     * @return Score
     */
    public function setQuestion(\AppBundle\Entity\Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return AppBundle\Entity\Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set evaluation
     *
     * @param AppBundle\Entity\Evaluation $evaluation
     * @return Score
     */
    public function setEvaluation(\AppBundle\Entity\Evaluation $evaluation = null)
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    /**
     * Get evaluation
     *
     * @return AppBundle\Entity\Evaluation 
     */
    public function getEvaluation()
    {
        return $this->evaluation;
    }
}
