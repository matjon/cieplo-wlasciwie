<?php

namespace Kraken\WarmBundle\Tests\Service;

use Kraken\WarmBundle\Entity\Calculation;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Entity\Layer;
use Kraken\WarmBundle\Entity\Wall;
use Kraken\WarmBundle\Entity\Material;
use Kraken\WarmBundle\Service\InstanceService;
use Kraken\WarmBundle\Service\Building;
use Kraken\WarmBundle\Service\DoubleBuilding;
use Kraken\WarmBundle\Service\VentilationService;
use Kraken\WarmBundle\Service\WallService;
use Kraken\WarmBundle\Service\WallFactory;
use Mockery;

class BuildingTest extends \PHPUnit_Framework_TestCase
{
    protected function makeInstance()
    {
        $m = new Material();
        $m->setName('stuff');
        $m->setLambda(0.2);

        $layer = new Layer();
        $layer->setMaterial($m);
        $layer->setSize(10);

        $wall = new Wall();
        $wall->setConstructionLayer($layer);

        $house = new House();
        $house->setBuildingWidth(10);
        $house->setBuildingLength(10);
        $house->setVentilationType('natural');
        $house->addWall($wall);
        $wall->setHouse($house);

        $calc = new Calculation();
        $calc->setHouse($house);
        $calc->setIndoorTemperature(20);

        $instance = new InstanceService();
        $instance->setCalculation($calc);

        return $instance;
    }

    public function testEnergyLossThroughGroundFloor()
    {
        $instance = $this->makeInstance();
        $building = new Building($instance , $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(29.21, $building->getEnergyLossThroughGroundFloor());

        $m2 = new Material();
        $m2->setName('warm stuff');
        $m2->setLambda(0.02);

        $isolation = new Layer();
        $isolation->setMaterial($m2);
        $isolation->setSize(20);

        $instance->get()->getHouse()->setGroundFloorIsolationLayer($isolation);

        $this->assertEquals(7.84, $building->getEnergyLossThroughGroundFloor());
    }

    public function testEnergyLossToUnderground()
    {
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setHasBasement(true);
        $instance->get()->getHouse()->setNumberFloors(3);
        $instance->get()->getHouse()->setNumberHeatedFloors(3);

        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(25.18, $building->getEnergyLossToUnderground());

        $m2 = new Material();
        $m2->setName('warm stuff');
        $m2->setLambda(0.02);

        $isolation = new Layer();
        $isolation->setMaterial($m2);
        $isolation->setSize(20);

        $instance->get()->getHouse()->setBasementFloorIsolationLayer($isolation);

        $this->assertEquals(14.12, $building->getEnergyLossToUnderground());

        $instance->get()->getHouse()->setHasBasement(true);
        $instance->get()->getHouse()->setNumberHeatedFloors(2);
        $instance->get()->getHouse()->setWhatsUnheated('basement');

        $this->assertEquals(0, $building->getEnergyLossToUnderground());
    }

    public function testFloorEnergyLossToUnheated()
    {
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setHasBasement(true);
        $instance->get()->getHouse()->setNumberFloors(3);
        $instance->get()->getHouse()->setNumberHeatedFloors(3);
        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(0, $building->getFloorEnergyLossToUnheated());

        $m2 = new Material();
        $m2->setName('warm stuff');
        $m2->setLambda(0.02);

        $isolation = new Layer();
        $isolation->setMaterial($m2);
        $isolation->setSize(20);

        $instance->get()->getHouse()->setGroundFloorIsolationLayer($isolation);

        $this->assertEquals(25.18, $building->getEnergyLossToUnderground());

        $instance->get()->getHouse()->setHasBasement(true);
        $instance->get()->getHouse()->setNumberHeatedFloors(2);
        $instance->get()->getHouse()->setWhatsUnheated('basement');

        $this->assertEquals(7.95, $building->getFloorEnergyLossToUnheated());
    }

    public function testHouseCubature()
    {
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setHasBasement(true);
        $instance->get()->getHouse()->setNumberFloors(4);
        $instance->get()->getHouse()->setNumberHeatedFloors(3);
        $instance->get()->getHouse()->setWhatsUnheated('basement');
        $instance->get()->getHouse()->setRoofType('flat');

        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(round(9.2*9.2*3*2.6, 2), $building->getHouseCubature());

        $instance->get()->getHouse()->setNumberFloors(2);
        $instance->get()->getHouse()->setHasBasement(false);
        $instance->get()->getHouse()->setNumberHeatedFloors(2);
        $instance->get()->getHouse()->setRoofType('oblique');

        $this->assertEquals(round(9.2*9.2*2.6*1.5, 2), $building->getHouseCubature());
    }

    public function testWallArea()
    {
        $instance = $this->makeInstance();
        $instance->get()->setBuildingType('single_house');
        $instance->get()->getHouse()->setBuildingLength(10);
        $instance->get()->getHouse()->setBuildingWidth(10);
        $instance->get()->getHouse()->setNumberFloors(1);
        $instance->get()->getHouse()->setHasBasement(false);
        $instance->get()->getHouse()->setNumberHeatedFloors(1);
        $instance->get()->getHouse()->setRoofType('flat');

        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(4, $building->getNumberOfWalls());
        $this->assertEquals(2.6, $building->getHouseHeight());
        $this->assertEquals(104, $building->getWallArea($instance->get()->getHouse()->getWalls()->first()));

        $instance = $this->makeInstance();
        $instance->get()->setBuildingType('double_house');
        $instance->get()->getHouse()->setBuildingLength(10);
        $instance->get()->getHouse()->setBuildingWidth(10);
        $instance->get()->getHouse()->setNumberFloors(1);
        $instance->get()->getHouse()->setHasBasement(false);
        $instance->get()->getHouse()->setNumberHeatedFloors(1);
        $instance->get()->getHouse()->setRoofType('flat');

        $building = new DoubleBuilding($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(3, $building->getNumberOfWalls());
        $this->assertEquals(2.6, $building->getHouseHeight());
        $this->assertEquals(78, $building->getWallArea($instance->get()->getHouse()->getWalls()->first()));
    }

    public function testNumberOfHeatedFloors()
    {
        //2 piętra, dach skosny - ogrzewany tylko parter
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setNumberFloors(2);
        $instance->get()->getHouse()->setNumberHeatedFloors(1);
        $instance->get()->getHouse()->setHasBasement(false);
        $instance->get()->getHouse()->setRoofType('oblique');
        $instance->get()->getHouse()->setWhatsUnheated('attic');

        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(1, $building->getNumberOfHeatedFloors());
        $this->assertTrue($building->isGroundFloorHeated());
        $this->assertFalse($building->isAtticHeated());
        $this->assertFalse($building->isBasementHeated());

        // parterówka, płaski dach, - ogrzewany tylko parter
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setNumberFloors(1);
        $instance->get()->getHouse()->setHasBasement(false);
        $instance->get()->getHouse()->setRoofType('flat');
        $instance->get()->getHouse()->setNumberHeatedFloors(1);

        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(1, $building->getNumberOfHeatedFloors());
        $this->assertTrue($building->isGroundFloorHeated());
        $this->assertFalse($building->isAtticHeated());
        $this->assertFalse($building->isBasementHeated());

        // 4 piętra, piwnica i skośny dach - ogrzewany parter i piętro
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setNumberFloors(4);
        $instance->get()->getHouse()->setHasBasement(true);
        $instance->get()->getHouse()->setWhatsUnheated('basement');
        $instance->get()->getHouse()->setRoofType('oblique');
        $instance->get()->getHouse()->setNumberHeatedFloors(2);

        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(2, $building->getNumberOfHeatedFloors());
        $this->assertTrue($building->isGroundFloorHeated());
        $this->assertFalse($building->isAtticHeated());
        $this->assertFalse($building->isBasementHeated());

        // parter i skośny dach - ogrzewany parter ino, źle podana liczba pieter ogolem
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setNumberFloors(1);
        $instance->get()->getHouse()->setHasBasement(false);
        $instance->get()->getHouse()->setRoofType('steep');
        $instance->get()->getHouse()->setNumberHeatedFloors(1);

        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(1, $building->getNumberOfHeatedFloors());
        $this->assertTrue($building->isGroundFloorHeated());
        $this->assertFalse($building->isAtticHeated());
        $this->assertFalse($building->isBasementHeated());

        // podane 2 piętra, piwnica, skosny dach, piwnica nieogrzewana - powinny być 2 piętra ogrzewane
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setNumberFloors(2);
        $instance->get()->getHouse()->setHasBasement(true);
        $instance->get()->getHouse()->setWhatsUnheated('basement');
        $instance->get()->getHouse()->setRoofType('steep');
        $instance->get()->getHouse()->setNumberHeatedFloors(2);

        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(2, $building->getNumberOfHeatedFloors());
        $this->assertTrue($building->isGroundFloorHeated());
        $this->assertTrue($building->isAtticHeated());
        $this->assertFalse($building->isBasementHeated());

        // podane 1 piętro, piwnica, płaski dach - zgadujemy ze ogrzewany tylko parter
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setNumberFloors(1);
        $instance->get()->getHouse()->setHasBasement(true);
        $instance->get()->getHouse()->setWhatsUnheated('');
        $instance->get()->getHouse()->setRoofType('flat');
        $instance->get()->getHouse()->setNumberHeatedFloors(1);

        $building = new Building($instance, $this->mockVentilation(), $this->mockWall(), $this->mockWallFactory());

        $this->assertEquals(1, $building->getNumberOfHeatedFloors());
        $this->assertTrue($building->isGroundFloorHeated());
        $this->assertFalse($building->isAtticHeated());
        $this->assertFalse($building->isBasementHeated());
    }

    public function testExternalWallEnergyLossFactor()
    {
        $instance = $this->makeInstance();
        $instance->get()->getHouse()->setNumberFloors(2);
        $instance->get()->getHouse()->setNumberHeatedFloors(2);
        $instance->get()->getHouse()->setNumberDoors(2);
        $instance->get()->getHouse()->setNumberWindows(10);
        $instance->get()->getHouse()->setBuildingLength(9.5);
        $instance->get()->getHouse()->setBuildingWidth(10.5);
        $instance->get()->getHouse()->setHasBasement(false);
        $instance->get()->getHouse()->setHasBalcony(false);
        $instance->get()->getHouse()->setRoofType('flat');
        $instance->get()->getHouse()->setDoorsType('old_wooden');
        $instance->get()->getHouse()->setWindowsType('new_double_glass');

        $m1 = new Material;
        $m1->setLambda(0.65);
        $m1->setName('pustak żużlobetonowy');

        $m2 = new Material;
        $m2->setName('styropian');
        $m2->setLambda(0.038);

        $l1 = new Layer;
        $l1->setMaterial($m1);
        $l1->setSize(40);

        $l2 = new Layer;
        $l2->setMaterial($m2);
        $l2->setSize(12);

        $w = new Wall();
        $w->setConstructionLayer($l1);
        $w->setHouse($instance->get()->getHouse());

        $instance->get()->getHouse()->addWall($w);

        $building = new Building($instance, $this->mockVentilation(), new WallService($instance), $this->mockWallFactory());

        $this->assertEquals(320.8492, $building->getExternalWallEnergyLossFactor($w));

        $w->setExtraIsolationLayer($l2);

        $this->assertEquals(53.1468, $building->getExternalWallEnergyLossFactor($w));
    }

    protected function mockVentilation()
    {
        $mock = Mockery::mock('Kraken\WarmBundle\Service\VentilationService');

        return $mock;
    }

    protected function mockWall()
    {
        $mock = Mockery::mock('Kraken\WarmBundle\Service\WallService');
        $mock->shouldReceive('getSize')->andReturn(0.4);
        $mock->shouldReceive('getThermalConductance')->andReturn(0.25);

        return $mock;
    }

    protected function mockWallFactory()
    {
        $mock = Mockery::mock('Kraken\WarmBundle\Service\WallFactory');

        return $mock;
    }
}
