<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Temperature
 *
 * @ORM\Table(name="temperature")
 * @ORM\Entity
 */
class Temperature
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
     * @var integer
     *
     * @ORM\Column(name="temperature", type="integer")
     */
    private $temperature;

    /**
     * @var integer
     *
     * @ORM\Column(name="time", type="integer")
     */
    private $time;


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
     * Set temperature
     *
     * @param integer $temperature
     * @return Temperature
     */
    public function setTemperature($temperature)
    {
        $this->temperature = $temperature;

        return $this;
    }

    /**
     * Get temperature
     *
     * @return integer 
     */
    public function getTemperature()
    {
        return $this->temperature;
    }

    /**
     * Set time
     *
     * @param integer $time
     * @return Temperature
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return integer 
     */
    public function getTime()
    {
        return $this->time;
    }
}
