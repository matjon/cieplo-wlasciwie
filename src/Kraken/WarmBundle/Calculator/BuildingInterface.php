<?php

namespace Kraken\WarmBundle\Calculator;

interface BuildingInterface
{
    /**
     * @return double
     */
    public function getEnergyLossToOutside();

    /**
     * @return double
     */
    public function getEnergyLossToUnheated();
    public function getHeatedArea();
    public function getNumberOfWalls();

    /**
     * @return \Kraken\WarmBundle\Entity\House|null
     */
    public function getHouse();
    public function getEnergyLossBreakdown();
    public function getHouseDescription();
}
