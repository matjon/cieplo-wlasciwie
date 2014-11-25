<?php

namespace Kraken\WarmBundle\Calculator;

use Doctrine\ORM\EntityManager;
use Kraken\WarmBundle\Service\InstanceService;
use Kraken\WarmBundle\Calculator\HeatingSeason;

class EnergyPricing
{
    protected $em;
    protected $instance;
    protected $calculator;
    protected $heatingSeason;

    public function __construct(InstanceService $instance, EnergyCalculator $calculator, EntityManager $em, HeatingSeason $heatingSeason)
    {
        $this->instance = $instance->get();
        $this->calculator = $calculator;
        $this->em = $em;
        $this->heatingSeason = $heatingSeason;
    }

    public function getEnergySourcesComparison()
    {
        $comparison = array();
        $energyAmount = $this->calculator->getYearlyEnergyConsumption();
        $fuels = $this->em
            ->createQueryBuilder()
            ->select('f')
            ->from('KrakenWarmBundle:Fuel', 'f')
            ->getQuery()
            ->getResult();

        foreach ($fuels as $fuel) {
            $amount = ($energyAmount/$fuel->getEfficiency())/($fuel->getEnergy()*0.277);
            $comparison[$fuel->getType()] = array(
                'label' => $fuel->getName(),
                'detail' => $fuel->getDetail(),
                'amount' => $amount,
                'price' => $fuel->getPrice(),
                'trade_amount' => $fuel->getTradeAmount(),
                'trade_unit' => $fuel->getTradeUnit(),
                'efficiency' => $fuel->getEfficiency(),
            );
        }

        return $comparison;
    }

    public function getDefaultWorkHourPrice()
    {
        return 8;
    }

    public function getFuels()
    {
        return $this->em
            ->createQueryBuilder()
            ->select('f')
            ->from('KrakenWarmBundle:Fuel', 'f')
            ->groupBy('f.name')
            ->orderBy('f.id')
            ->getQuery()
            ->getResult();
    }

    public function getYearlyServiceTime($fuelType) {
        $yearlyFuelPreparationTimes = array(
            'coal_dirty' => 3,
            'coal_cleaner' => 3,
            'coal_automatic' => 2,
            'coal_sand' => 5,
            'coke' => 3,
            'gas' => 0,
            'pellet' => 2,
            'electricity' => 0,
            'wood' => 10,
        );

        $yearlyStovePreparationTimes = array(
            'coal_dirty' => 15,
            'coal_cleaner' => 10,
            'coal_sand' => 15,
            'coal_automatic' => 5,
            'coke' => 10,
            'gas' => 2,
            'pellet' => 5,
            'electricity' => 0,
            'wood' => 10,
        );

        $dailyServiceTimes = array(
            'coal_dirty' => 2,
            'coal_cleaner' => 0.75,
            'coke' => 0.75,
            'coal_sand' => 0.75,
            'coal_automatic' => 0.25,
            'gas' => 0,
            'pellet' => 0.25,
            'electricity' => 0,
            'wood' => 1.5,
        );

        $heatingSeasonDays = $this->heatingSeason->getSeasonLength();

        return $yearlyFuelPreparationTimes[$fuelType] + $yearlyStovePreparationTimes[$fuelType] + $heatingSeasonDays * 0.75 * $dailyServiceTimes[$fuelType];
    }
}
