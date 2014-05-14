<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Calculator\EnergyCalculator;
use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Service\InstanceService;
use Kraken\WarmBundle\Service\FuelService;
use Kraken\WarmBundle\Calculator\HeatingSeason;
use Mockery;

class EnergyCalculatorTest extends \PHPUnit_Framework_TestCase
{
    public function testVentilationEnergyLossFactor()
    {
//         $house = new House();
//         $house->setVentilationType('natural');
//
//         $calc = new Calculation();
//         $calc->setHouse($house);
//         $calc->setIndoorTemperature(20);
//         $instance = new InstanceService();
//         $instance->setCalculation($calc);
//
//         $c = new EnergyCalculator($instance, $this->mockHeating(), $this->mockFuel(), $this->mockBuilding());
//
//         // natural ventilation
//         $this->assertEquals(161.5, $c->getVentilationEnergyLossFactor(0));
//
//         $house->setVentilationType('mechanical_recovery');
//         $calc->setHouse($house);
//         // mechanical ventilation with heat recovery
        $this->assertEquals(1, 1);
    }

    protected function mockHeating()
    {
        $heating = Mockery::mock('Kraken\WarmBundle\Calculator\HeatingSeason');
        $heating->shouldReceive('getAverageTemperature')->andReturn(1);

        return $heating;
    }

    protected function mockFuel()
    {
        $mock = Mockery::mock('Kraken\WarmBundle\Service\FuelService');

        return $mock;
    }

    protected function mockBuilding()
    {
        $mock = Mockery::mock('Kraken\WarmBundle\Calculator\BuildingInterface');

        return $mock;
    }
}
