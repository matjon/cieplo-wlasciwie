<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="material")
 */
class Material
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=5)
     */
    protected $lambda;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $for_wall_construction_layer;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $for_wall_internal_layer;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $for_wall_facade_layer;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $for_wall_isolation_layer;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $for_ceiling;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $for_floor;

    /**
     * @ORM\OneToMany(targetEntity="Layer", mappedBy="material", cascade={"all"})
     */
    protected $layers;

    public function __toString()
    {
        return $this->name;
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
     * Set name
     *
     * @param  \varchar $name
     * @return Material
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return \varchar
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set for_wall_construction_layer
     *
     * @param  boolean  $forWallConstructionLayer
     * @return Material
     */
    public function setForWallConstructionLayer($forWallConstructionLayer)
    {
        $this->for_wall_construction_layer = $forWallConstructionLayer;

        return $this;
    }

    /**
     * Get for_wall_construction_layer
     *
     * @return boolean
     */
    public function getForWallConstructionLayer()
    {
        return $this->for_wall_construction_layer;
    }

    /**
     * Set for_wall_internal_layer
     *
     * @param  boolean  $forWallInternalLayer
     * @return Material
     */
    public function setForWallInternalLayer($forWallInternalLayer)
    {
        $this->for_wall_internal_layer = $forWallInternalLayer;

        return $this;
    }

    /**
     * Get for_wall_internal_layer
     *
     * @return boolean
     */
    public function getForWallInternalLayer()
    {
        return $this->for_wall_internal_layer;
    }

    /**
     * Set for_wall_facade_layer
     *
     * @param  boolean  $forWallFacadeLayer
     * @return Material
     */
    public function setForWallFacadeLayer($forWallFacadeLayer)
    {
        $this->for_wall_facade_layer = $forWallFacadeLayer;

        return $this;
    }

    /**
     * Get for_wall_facade_layer
     *
     * @return boolean
     */
    public function getForWallFacadeLayer()
    {
        return $this->for_wall_facade_layer;
    }

    /**
     * Set for_wall_isolation_layer
     *
     * @param  boolean  $forWallIsolationLayer
     * @return Material
     */
    public function setForWallIsolationLayer($forWallIsolationLayer)
    {
        $this->for_wall_isolation_layer = $forWallIsolationLayer;

        return $this;
    }

    /**
     * Get for_wall_isolation_layer
     *
     * @return boolean
     */
    public function getForWallIsolationLayer()
    {
        return $this->for_wall_isolation_layer;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->layers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add layers
     *
     * @param  \Kraken\WarmBundle\Entity\Layer $layers
     * @return Material
     */
    public function addLayer(\Kraken\WarmBundle\Entity\Layer $layers)
    {
        $this->layers[] = $layers;

        return $this;
    }

    /**
     * Remove layers
     *
     * @param \Kraken\WarmBundle\Entity\Layer $layers
     */
    public function removeLayer(\Kraken\WarmBundle\Entity\Layer $layers)
    {
        $this->layers->removeElement($layers);
    }

    /**
     * Get layers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLayers()
    {
        return $this->layers;
    }

    /**
     * Set lambda
     *
     * @param  float    $lambda
     * @return Material
     */
    public function setLambda($lambda)
    {
        $this->lambda = $lambda;

        return $this;
    }

    /**
     * Get lambda
     *
     * @return float
     */
    public function getLambda()
    {
        return $this->lambda;
    }

    /**
     * Set for_ceiling
     *
     * @param  boolean  $forCeiling
     * @return Material
     */
    public function setForCeiling($forCeiling)
    {
        $this->for_ceiling = $forCeiling;

        return $this;
    }

    /**
     * Get for_ceiling
     *
     * @return boolean
     */
    public function getForCeiling()
    {
        return $this->for_ceiling;
    }

    /**
     * Set for_floor
     *
     * @param  boolean  $forFloor
     * @return Material
     */
    public function setForFloor($forFloor)
    {
        $this->for_floor = $forFloor;

        return $this;
    }

    /**
     * Get for_floor
     *
     * @return boolean
     */
    public function getForFloor()
    {
        return $this->for_floor;
    }
}
