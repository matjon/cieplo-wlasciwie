<?php

namespace Kraken\WarmBundle\Calculator;

interface BuildingInterface
{
    public function getEnergyLossToOutside();
    public function getEnergyLossToUnheated();
    public function getHeatedArea();
    public function getNumberOfWalls();
    public function getHouse();
    public function getEnergyLossBreakdown();
    public function getHouseDescription();
}
