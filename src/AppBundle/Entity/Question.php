<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\QuestionRepository")
 */
class Question
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", length=500, unique=false)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Level")
     * @ORM\JoinColumn(name="level_id", referencedColumnName="id", nullable=false)
     */
    private $level;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     */
    private $duration;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Topic")
     * @ORM\JoinColumn(name="topic_id", referencedColumnName="id", nullable=false)
     */
    private $topic;


    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Response", mappedBy="question", cascade={"all"}, orphanRemoval=true)
     */
    private $responses;    
    
    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Evaluation", mappedBy="questions")
     * @ORM\JoinTable(name="evaluation_question")
     */
    private $evaluations;
    
    public function __construct()
    {
        $this->responses = new ArrayCollection();
        $this->topic = new ArrayCollection();
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
     * Set content
     *
     * @param string $content
     *
     * @return Question
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return Question
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set level
     *
     * @param \AppBundle\Entity\Level $level
     *
     * @return Question
     */
    public function setLevel(\AppBundle\Entity\Level $level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return \AppBundle\Entity\Level
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set topic
     *
     * @param \AppBundle\Entity\Topic $topic
     *
     * @return Question
     */
    public function setTopic(\AppBundle\Entity\Topic $topic)
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Get topic
     *
     * @return \AppBundle\Entity\Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Add response
     *
     * @param \AppBundle\Entity\Response $response
     *
     * @return Question
     */
    public function addResponse(\AppBundle\Entity\Response $response)
    {
    	$response->setQuestion($this);
    	$this->responses[] = $response;        

        return $this;
    }

    /**
     * Remove Response
     *
     * @param \AppBundle\Entity\Response $response
     */
    public function removeResponse(\AppBundle\Entity\Response $response)
    {
        $this->responses->removeElement($response);
    }

    /**
     * Get responses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResponses()
    {
        return $this->responses;
    }
    
}
