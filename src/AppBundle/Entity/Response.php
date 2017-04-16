<?php

namespace AppBundle\Entity;

/**
 * Response
 */
class Response
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var boolean
     */
    private $isCorrect;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Question
     */
    private $question;


    /**
     * Set text
     *
     * @param string $text
     *
     * @return Response
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set isCorrect
     *
     * @param boolean $isCorrect
     *
     * @return Response
     */
    public function setIsCorrect($isCorrect)
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }

    /**
     * Get isCorrect
     *
     * @return boolean
     */
    public function getIsCorrect()
    {
        return $this->isCorrect;
    }

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
     * Set question
     *
     * @param \AppBundle\Entity\Question $question
     *
     * @return Response
     */
    public function setQuestion(\AppBundle\Entity\Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \AppBundle\Entity\Question
     */
    public function getQuestion()
    {
        return $this->question;
    }
}

