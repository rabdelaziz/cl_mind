<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Difficulty
 *
 * @ORM\Table(name="difficulty")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DifficultyRepository")
 */
class Difficulty
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
     * @ORM\Column(name="name", type="string", length=16, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=45, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="percentage_easy", type="integer")
     */
    private $percentageEasy;

    /**
     * @var int
     *
     * @ORM\Column(name="percentage_average", type="integer")
     */
    private $percentageAverage;

    /**
     * @var int
     *
     * @ORM\Column(name="percentage_difficult", type="integer")
     */
    private $percentageDifficult;


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
     * Set name
     *
     * @param string $name
     *
     * @return Difficulty
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Difficulty
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set percentageEasy
     *
     * @param integer $percentageEasy
     *
     * @return Difficulty
     */
    public function setPercentageEasy($percentageEasy)
    {
        $this->percentageEasy = $percentageEasy;

        return $this;
    }

    /**
     * Get percentageEasy
     *
     * @return int
     */
    public function getPercentageEasy()
    {
        return $this->percentageEasy;
    }

    /**
     * Set percentageAverage
     *
     * @param integer $percentageAverage
     *
     * @return Difficulty
     */
    public function setPercentageAverage($percentageAverage)
    {
        $this->percentageAverage = $percentageAverage;

        return $this;
    }

    /**
     * Get percentageAverage
     *
     * @return int
     */
    public function getPercentageAverage()
    {
        return $this->percentageAverage;
    }

    /**
     * Set percentageDifficult
     *
     * @param integer $percentageDifficult
     *
     * @return Difficulty
     */
    public function setPercentageDifficult($percentageDifficult)
    {
        $this->percentageDifficult = $percentageDifficult;

        return $this;
    }

    /**
     * Get percentageDifficult
     *
     * @return int
     */
    public function getPercentageDifficult()
    {
        return $this->percentageDifficult;
    }
}

