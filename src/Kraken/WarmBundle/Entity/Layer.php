<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="layer")
 */
class Layer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="decimal", nullable=false)
     * @Assert\Range(min="1", minMessage = "Min. grubość warstwy to 1cm", max="1000", maxMessage = "Masz ścianę powyżej 10m grubości? To już bunkier!")
     */
    protected $size;

    /**
     * @ORM\ManyToOne(targetEntity="Material", inversedBy="layers")
     * @ORM\JoinColumn(name="material_id", referencedColumnName="id", nullable=false)
     */
    protected $material;

    public function __toString()
    {
        return (string) $this->getSize();
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
     * Set size
     *
     * @param  float $size
     * @return Layer
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get layer size in meters
     *
     * @return float
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set wall
     *
     * @param  \Kraken\WarmBundle\Entity\Wall $wall
     * @return Layer
     */
    public function setWall(\Kraken\WarmBundle\Entity\Wall $wall)
    {
        $this->wall = $wall;

        return $this;
    }

    /**
     * Get wall
     *
     * @return \Kraken\WarmBundle\Entity\Wall
     */
    public function getWall()
    {
        return $this->wall;
    }

    /**
     * Set material
     *
     * @param  \Kraken\WarmBundle\Entity\Material $material
     * @return Layer
     */
    public function setMaterial(\Kraken\WarmBundle\Entity\Material $material = null)
    {
        $this->material = $material;

        return $this;
    }

    /**
     * Get material
     *
     * @return \Kraken\WarmBundle\Entity\Material
     */
    public function getMaterial()
    {
        return $this->material;
    }
}
