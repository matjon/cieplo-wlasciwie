<?php

namespace Kraken\WarmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fuel")
 */
class Fuel
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $detail;

    /**
     * @ORM\Column(type="string", length=3)
     */
    protected $unit;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2)
     */
    protected $price;

    /**
     * @ORM\Column(type="integer")
     */
    protected $trade_amount;

    /**
     * @ORM\Column(type="string", length=3)
     */
    protected $trade_unit;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2)
     */
    protected $energy;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=2)
     */
    protected $efficiency;

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
     * Set type
     *
     * @param  string $type
     * @return Fuel
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Fuel
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
     * Set detail
     *
     * @param  string $detail
     * @return Fuel
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Get detail
     *
     * @return string
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Set unit
     *
     * @param  string $unit
     * @return Fuel
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set price
     *
     * @param  integer $price
     * @return Fuel
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set trade_amount
     *
     * @param  integer $tradeAmount
     * @return Fuel
     */
    public function setTradeAmount($tradeAmount)
    {
        $this->trade_amount = $tradeAmount;

        return $this;
    }

    /**
     * Get trade_amount
     *
     * @return integer
     */
    public function getTradeAmount()
    {
        return $this->trade_amount;
    }

    /**
     * Set trade_unit
     *
     * @param  string $tradeUnit
     * @return Fuel
     */
    public function setTradeUnit($tradeUnit)
    {
        $this->trade_unit = $tradeUnit;

        return $this;
    }

    /**
     * Get trade_unit
     *
     * @return string
     */
    public function getTradeUnit()
    {
        return $this->trade_unit;
    }

    /**
     * Set energy
     *
     * @param  float $energy
     * @return Fuel
     */
    public function setEnergy($energy)
    {
        $this->energy = $energy;

        return $this;
    }

    /**
     * Get energy
     *
     * @return float
     */
    public function getEnergy()
    {
        return $this->energy;
    }

    /**
     * Set efficiency
     *
     * @param  float $efficiency
     * @return Fuel
     */
    public function setEfficiency($efficiency)
    {
        $this->efficiency = $efficiency;

        return $this;
    }

    /**
     * Get efficiency
     *
     * @return float
     */
    public function getEfficiency()
    {
        return $this->efficiency;
    }
}
