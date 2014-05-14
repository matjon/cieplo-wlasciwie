<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="city")
 */
class City
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="decimal", scale=7)
     */
    protected $latitude;

    /**
     * @ORM\Column(type="decimal", scale=7)
     */
    protected $longitude;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Temperature", mappedBy="city")
     */
    protected $temperatures;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->temperatures = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set latitude
     *
     * @param  float $latitude
     * @return City
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param  float $longitude
     * @return City
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return City
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
     * Add temperatures
     *
     * @param  \Kraken\WarmBundle\Entity\Temperature $temperatures
     * @return City
     */
    public function addTemperature(\Kraken\WarmBundle\Entity\Temperature $temperatures)
    {
        $this->temperatures[] = $temperatures;

        return $this;
    }

    /**
     * Remove temperatures
     *
     * @param \Kraken\WarmBundle\Entity\Temperature $temperatures
     */
    public function removeTemperature(\Kraken\WarmBundle\Entity\Temperature $temperatures)
    {
        $this->temperatures->removeElement($temperatures);
    }

    /**
     * Get temperatures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTemperatures()
    {
        return $this->temperatures;
    }
}
