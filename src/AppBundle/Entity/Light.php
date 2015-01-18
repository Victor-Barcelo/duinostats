<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Light
 *
 * @ORM\Table(name="light")
 * @ORM\Entity
 */
class Light
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
     * @ORM\Column(name="light", type="integer")
     */
    private $light;

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
     * Set light
     *
     * @param integer $light
     * @return Light
     */
    public function setLight($light)
    {
        $this->light = $light;

        return $this;
    }

    /**
     * Get light
     *
     * @return integer 
     */
    public function getLight()
    {
        return $this->light;
    }

    /**
     * Set time
     *
     * @param integer $time
     * @return Light
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
