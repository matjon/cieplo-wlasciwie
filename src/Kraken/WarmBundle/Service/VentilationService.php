<?php

namespace Kraken\WarmBundle\Service;

use Kraken\WarmBundle\Entity\House;

class VentilationService
{
    const FLOOR_HEIGHT = 2.6;
    const CEILING_THICKNESS = 0.35;

    protected $instance;
    protected $house_service;

    public function __construct(InstanceService $instance)
    {
        $this->instance = $instance->get();
    }

    public function getAirStream(Building $building)
    {
        $airCapacity = $building->getHouseCubature();
        $exchangeMultiplicity = $this->getAirExchangeMultiplicity($building);
        $neighbourhoodClosenessFactor = 0.03;
        $buildingHeightFactor = $building->getHouseHeight() > 10 ? 1.2 : 1;

        $infiltration = $airCapacity * $exchangeMultiplicity * $neighbourhoodClosenessFactor * $buildingHeightFactor;

        return max($infiltration, $this->getMinimalAirStream($building));
    }

    public function getMinimalAirStream(Building $building)
    {
        $airCapacity = $building->getHouseCubature();

        return 0.5 * $airCapacity;
    }

    public function getAirExchangeMultiplicity(Building $building)
    {
        $house = $building->getHouse();
        $type = $house->getVentilationType();
        $windowsType = $house->getWindowsType();

        $year = $this->instance->getConstructionYear();

        $exchangeThroughLeaks = 0;

        if ($year < 1950) {
            $exchangeThroughLeaks = 0.5;
        } elseif ($year < 1990) {
            $exchangeThroughLeaks = 0.25;
        }

        if ($type == 'natural') {
            if ($windowsType == 'old_single_glass') {
                return 4 + $exchangeThroughLeaks;
            } elseif ($windowsType == 'old_improved') {
                return 2.5 + $exchangeThroughLeaks;
            } elseif ($windowsType == 'old_double_glass') {
                return 2 + $exchangeThroughLeaks;
            } elseif ($windowsType == 'semi_new_double_glass') {
                return 1.5 + $exchangeThroughLeaks;
            }

            return 1.25;
        }

        return 0.8;
    }
}
