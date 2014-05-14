<?php

namespace Kraken\WarmBundle\Calculator;

use Kraken\WarmBundle\Service\InstanceService;

class PunchLineGenerator
{
    protected $instance;
    protected $calculator;

    public function __construct(InstanceService $instance, EnergyCalculator $calculator)
    {
        $this->instance = $instance->get();
        $this->calculator = $calculator;
    }

    public function getPhrases()
    {
        $factor = $this->calculator->getYearlyEnergyConsumptionFactor();
        $type = $this->instance->getBuildingType();

        $house = "Twój dom to dziura bez dna";

        if ($factor <= 20) {
            $house = "Twój dom jest pasywny";
        } elseif ($factor <= 45) {
            $house = "Twój dom jest niskoenergetyczny";
        } elseif ($factor < 80) {
            $house = "Twój dom jest energooszczędny";
        } elseif ($factor < 100) {
            $house = "Twój dom jest średnio energooszczędny";
        } elseif ($factor < 150) {
            $house = "Twój dom jest średnio energochłonny";
        }

        try {
            $efficiency = $this->calculator->getYearlyStoveEfficiency();
            $heating = "Ogrzewanie działa bardzo ekonomicznie";

            if ($this->instance->isUsingSolidFuel()) {
                $bad = 0.4;
                $quite = 0.5;
                $good = 0.7;
            } else {
                $bad = 0.5;
                $quite = 0.65;
                $good = 0.8;
            }

            if ($efficiency < $bad) {
                $heating = "Ogrzewanie jest kompletnie nieefektywne";
            } elseif ($efficiency < $quite) {
                $heating = "Ogrzewanie pracuje na skraju opłacalności";
            } elseif ($efficiency < $good) {
                $heating = "Ogrzewanie działa dość ekonomicznie";
            }
        } catch (\Exception $e) {
            $heating = "Nie wiemy nic o tym jak aktualnie ogrzewasz";
        }

        return array(
            'house' => $house,
            'heating' => $heating
        );
    }
}
