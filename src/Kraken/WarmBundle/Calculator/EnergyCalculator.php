<?php

namespace Kraken\WarmBundle\Calculator;

use Kraken\WarmBundle\Service\FuelService;
use Kraken\WarmBundle\Service\InstanceService;
use Kraken\WarmBundle\Calculator\BuildingInterface;

class EnergyCalculator
{
    protected $instance;
    protected $heating_season;
    protected $fuel_service;
    protected $building;
    protected $climate;

    public function __construct(InstanceService $instance, HeatingSeason $heatingSeason, FuelService $fuelService, BuildingInterface $building, ClimateZoneService $climate)
    {
        $this->instance = $instance->get();
        $this->heating_season = $heatingSeason;
        $this->fuel_service = $fuelService;
        $this->building = $building;
        $this->climate = $climate;
    }

    /*
     * Amount of energy in kWh needed during the whole heating season (depends on location)
     */
    public function getYearlyEnergyConsumption()
    {
        $heatingSeasonTemperatures = $this->heating_season->getDailyTemperatures();
        $energy = 0;

        foreach ($heatingSeasonTemperatures as $t) {
            $energy += $this->getHeatingPower($t->getValue()) * 24; // 24h
        }

        return $energy/1000;
    }

    public function getNecessaryStovePower($fuel = 'coal')
    {
        $power = $this->getMaxHeatingPower() / 1000;

        if ($fuel == 'sand_coal') {
            return 2 * $power;
        }

        return 1.1 * $power;
    }

    public function getYearlyEnergyConsumptionFactor()
    {
        return $this->getYearlyEnergyConsumption()/$this->building->getHeatedArea();
    }

    public function getYearlyStoveEfficiency()
    {
        $paidEnergy = $this->getEnergyOfSpentFuel();

        if ($paidEnergy == 0) {
            throw new \RuntimeException("Fuel consumption info not provided");
        }

        return $this->getYearlyEnergyConsumption()/$paidEnergy;
    }

    /*
     * Get energy in kWh contained in consumed amount of fuel
     */
    public function getEnergyOfSpentFuel()
    {
        // 10% as equivalent of kindling wood etc.
        return 1.1 * $this->fuel_service->getFuelEnergy($this->instance->getFuelType(), $this->instance->getFuelConsumption());
    }

    /*
     * Heat demand factor derived from maximum heating power
     */
    public function getHeatDemandFactor()
    {
        return $this->getMaxHeatingPower()/$this->building->getHeatedArea();
    }

    /*
     * Heating power in Watts needed for given outdoor temperature
     */
    public function getHeatingPower($outdoorTemp)
    {
        return ($this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated()) * $this->getTemperatureDifference($outdoorTemp);
    }

    /*
     * Returns maximum heating power needed for lowest outdoor temperatures
     */
    public function getMaxHeatingPower()
    {
        $outdoorTemp = $this->climate->getDesignOutdoorTemperature();

        return ($this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated()) * $this->getTemperatureDifference($outdoorTemp);
    }

    /*
     * Returns average heating power needed during heating season
     */
    public function getAvgHeatingPower()
    {
        $avgTemp = $this->heating_season->getAverageTemperature($this->instance);

        return ($this->building->getEnergyLossToOutside() + $this->building->getEnergyLossToUnheated()) * $this->getTemperatureDifference($avgTemp);
    }

    public function getTemperatureDifference($outdoorTemp)
    {
        return $this->instance->getIndoorTemperature() - $outdoorTemp;
    }

    public function isStoveOversized()
    {
        if (!$this->instance->isUsingSolidFuel()) {
            return false;
        }

        $actualStovePower = $this->instance->getStovePower();

        if (!$actualStovePower) {
            return false;
        }

        if ($this->instance->getFuelType() == 'sand_coal') {
            return $actualStovePower > 1.5 * $this->getNecessaryStovePower('sand_coal');
        }

        $factor = $this->getNecessaryStovePower() > 10 ? 1.5 : 2;

        return $actualStovePower > $factor * $this->getNecessaryStovePower();
    }

}
