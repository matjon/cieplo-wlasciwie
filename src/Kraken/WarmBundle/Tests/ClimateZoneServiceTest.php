<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Calculator\ClimateZoneService;
use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Service\InstanceService;
use Kraken\WarmBundle\Service\FuelService;
use Kraken\WarmBundle\Calculator\HeatingSeason;
use Mockery;

class ClimateZoneServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testClimateZone()
    {
        $house = new House();
        $house->setVentilationType('natural');

        $calc = new Calculation();
        $calc->setHouse($house);
        $calc->setIndoorTemperature(20);
        $calc->setLatitude(51.11);
        $calc->setLongitude(17.01);
        $instance = new InstanceService();
        $instance->setCalculation($calc);

        $c = new ClimateZoneService($instance);

        $this->assertEquals(2, $c->getClimateZone());

        // Wroclaw
        $calc->setLatitude(53.44);
        $calc->setLongitude(14.41);
        $this->assertEquals(1, $c->getClimateZone());

        // Szczecin
        $calc->setLatitude(54.41);
        $calc->setLongitude(16.42);
        $this->assertEquals(1, $c->getClimateZone());

        // Elbląg
        $calc->setLatitude(54.15);
        $calc->setLongitude(19.40);
        $this->assertEquals(1, $c->getClimateZone());

        // Brodnica
        $calc->setLatitude(53.25);
        $calc->setLongitude(19.38);
        $this->assertEquals(1, $c->getClimateZone());

        // Augustów
        $calc->setLatitude(53.84);
        $calc->setLongitude(22.98);
        $this->assertEquals(5, $c->getClimateZone());

        // Łomża
        $calc->setLatitude(53.17);
        $calc->setLongitude(22.07);
        $this->assertEquals(4, $c->getClimateZone());

        // Poznań
        $calc->setLatitude(52.40);
        $calc->setLongitude(16.94);
        $this->assertEquals(2, $c->getClimateZone());

        // Opole
        $calc->setLatitude(50.67);
        $calc->setLongitude(17.91);
        $this->assertEquals(3, $c->getClimateZone());

        // Nowy Sącz
        $calc->setLatitude(49.62);
        $calc->setLongitude(20.71);
        $this->assertEquals(3, $c->getClimateZone());

        // Zakopane
        $calc->setLatitude(49.29);
        $calc->setLongitude(19.95);
        $this->assertEquals(5, $c->getClimateZone());
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
