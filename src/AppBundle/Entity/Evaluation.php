<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;



/**
 * Evaluation
 *
 * @ORM\Table(name="evaluation")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\EvaluationRepository")

 */
class Evaluation
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
	 * @ORM\Column(name="title", type="string", length=50, nullable=true, unique=false)
	 */
	private $title;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="created_date", type="datetime")
	 */
	private $createdDate;

	/**
	 * @var \AppBundle\Entity\Difficulty
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Difficulty")
	 * @ORM\JoinColumn(name="difficulty_id", referencedColumnName="id", nullable=false)
	 */
	private $difficulty;
	
	/**
	 * @var \AppBundle\Entity\Status
	 * 
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Status")
	 * @ORM\JoinColumn(name="status_id", referencedColumnName="id", nullable=false)
	 */
	private $status;

	/**
	 * @var ArrayCollection $questions
	 *
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Question",  inversedBy="evaluations", cascade={"persist"})
	 * @ORM\JoinTable(name="evaluation_question")
	 */
	private $questions;

	/**
	 * @var \AppBundle\Entity\User
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
	 * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
	 */
	private $author;

	/**
	 * @var ArrayCollection $candidates
	 *
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", inversedBy="evaluations", cascade={"persist"})
	 * @ORM\JoinTable(name="evaluation_candidate")
	 */
	private $candidates;


	/**
	 *
	 * @var ArrayCollection $topics
	 */
	private $topics;


	/**
	 *
	 */
	public function __construct()
	{
		$this->createdDate = new \DateTime();
		$this->candidates = new ArrayCollection();
		$this->questions = new ArrayCollection();
		$this->topics = new ArrayCollection();
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
     * @param string $title
     *
     * @return Evaluation
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return Evaluation
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set difficulty
     *
     * @param \AppBundle\Entity\Difficulty $difficulty
     *
     * @return Evaluation
     */
    public function setDifficulty(\AppBundle\Entity\Difficulty $difficulty)
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    /**
     * Get difficulty
     *
     * @return \AppBundle\Entity\Difficulty
     */
    public function getDifficulty()
    {
        return $this->difficulty;
    }

    /**
     * Set status
     *
     * @param \AppBundle\Entity\Status $status
     *
     * @return Evaluation
     */
    public function setStatus(\AppBundle\Entity\Status $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \AppBundle\Entity\Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add question
     *
     * @param \AppBundle\Entity\Question $question
     *
     * @return Evaluation
     */
    public function addQuestion(\AppBundle\Entity\Question $question)
    {
        $this->questions[] = $question;

        return $this;
    }

    /**
     * Remove question
     *
     * @param \AppBundle\Entity\Question $question
     */
    public function removeQuestion(\AppBundle\Entity\Question $question)
    {
        $this->questions->removeElement($question);
    }

    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Set author
     *
     * @param \AppBundle\Entity\User $author
     *
     * @return Evaluation
     */
    public function setAuthor(\AppBundle\Entity\User $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \AppBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Add candidate
     *
     * @param \AppBundle\Entity\User $candidate
     *
     * @return Evaluation
     */
    public function addCandidate(\AppBundle\Entity\User $candidate)
    {
        $this->candidates[] = $candidate;

        return $this;
    }

    /**
     * Remove candidate
     *
     * @param \AppBundle\Entity\User $candidate
     */
    public function removeCandidate(\AppBundle\Entity\User $candidate)
    {
        $this->candidates->removeElement($candidate);
    }

    /**
     * Get candidates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCandidates()
    {
        return $this->candidates;
    }

    public function getQuestionsFiltered($ids) 
    {
        $criteria = Criteria::create()->where(Criteria::expr()->in("id", $ids));

        return $this->getQuestions()->matching($criteria); 
    }
}
