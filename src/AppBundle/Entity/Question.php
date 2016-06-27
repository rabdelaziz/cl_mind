<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QuestionRepository")
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
     * @ORM\Column(name="enonce", type="text", length=500, unique=false)
     */
    private $enonce;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Niveau")
     */
    private $niveau;

    /**
     * @var int
     *
     * @ORM\Column(name="duree", type="integer", nullable=true)
     */
    private $duree;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Topic")
     */
    private $topic;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Reponse", mappedBy="question", cascade={"persist"})
     */
    private $reponses;



    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set enonce
     *
     * @param string $enonce
     *
     * @return Question
     */
    public function setEnonce($enonce)
    {
        $this->enonce = $enonce;

        return $this;
    }

    /**
     * Get enonce
     *
     * @return string
     */
    public function getEnonce()
    {
        return $this->enonce;
    }

    /**
     * Set Niveau
     *
     * @param Niveau $niveau
     *
     * @return Question
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get Niveau
     *
     * @return Niveau
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * Set duree
     *
     * @param integer $duree
     *
     * @return Question
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;

        return $this;
    }

    /**
     * Get duree
     *
     * @return int
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * Set Topic
     *
     * @param Topic $topic
     *
     * @return Question
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Get Topic
     *
     * @return Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    public function addReponse(Reponse $reponse)
    {
        $this->reponses[] = $reponse;

        // On lie la question à la reponse
        $reponse->setQuestion($this);

        return $this;
    }

    public function removeReponse(Reponse $reponse)
    {
        $this->reponses->removeElement($reponse);
    }

    public function getReponses()
    {
        return $this->reponses;
    }
}

